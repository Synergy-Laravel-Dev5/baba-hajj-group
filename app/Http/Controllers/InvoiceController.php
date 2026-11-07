<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Company;
use App\Models\Bank;
use App\Models\ExpenseTransaction;
use App\Models\Transaction;
use App\Models\Booking;
use App\Enums\CurrencyType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('invoice_view');
        $status   = $request->get('status', 'all');
        $clientId = $request->get('client_id');
        $search   = $request->get('search');

        $query = Invoice::with(['client', 'items', 'bank']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('bill_to', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $clients  = Client::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total_invoiced' => (float) Invoice::sum('total_amount'),
            'total_paid'     => (float) Invoice::sum('payment_made'),
            'total_balance'  => (float) Invoice::sum('balance_due'),
            'count'          => Invoice::count(),
        ];

        return view('invoice.index', compact('invoices', 'clients', 'status', 'clientId', 'search', 'stats'));
    }

    public function create(Request $request)
    {
        $this->authorize('invoice_create');
        $selectedClientId = $request->get('client_id');
        $clients = Client::with('package')->orderBy('name')->get();

        $nextInvoiceNo = Invoice::nextInvoiceNo();
        $today = Carbon::now()->format('Y-m-d');

        // Pre-fill Bill To if client is provided via URL parameter
        $prefillBillTo = '';
        if ($selectedClientId) {
            $c = Client::find($selectedClientId);
            if ($c) {
                $lines = [strtoupper($c->name)];
                if ($c->company_name) {
                    $lines[] = $c->company_name;
                }
                if ($c->phone) {
                    $lines[] = "Contact: " . $c->phone;
                }
                if ($c->email) {
                    $lines[] = "Email: " . $c->email;
                }
                if ($c->passport_number) {
                    $lines[] = "Passport: " . $c->passport_number;
                }
                if ($c->cnic) {
                    $lines[] = "CNIC: " . $c->cnic;
                }
                $prefillBillTo = implode("\n", $lines);
            }
        }

        // Active Banks for payment instructions & deposits
        $banks = Bank::where('status', 'active')->orderBy('name')->get();

        // Currencies
        $currencies = [
            'PKR' => 'PKR (Pakistani Rupee)',
            'SAR' => 'SAR (Saudi Riyal)',
            'USD' => 'USD (US Dollar)',
            'GBP' => 'GBP (British Pound)',
            'AED' => 'AED (UAE Dirham)',
        ];

        return view('invoice.create', compact('clients', 'nextInvoiceNo', 'today', 'selectedClientId', 'prefillBillTo', 'banks', 'currencies'));
    }

    public function store(Request $request)
    {
        $this->authorize('invoice_create');
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'bank_id'          => 'nullable|exists:banks,id',
            'invoice_no'       => 'required|string|max:50|unique:invoices,invoice_no',
            'invoice_date'     => 'required|date',
            'due_date'         => 'required|date',
            'currency'         => 'required|string|max:10',
            'bill_to'          => 'required|string',
            'items'            => 'required|array|min:1',
            'items.*.rate'     => 'required|numeric|min:0',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'discount'         => 'nullable|numeric|min:0',
            'payment_made'     => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ], [
            'client_id.required'  => 'Please select a Client.',
            'invoice_no.required' => 'Invoice Number is required.',
            'invoice_no.unique'   => 'This Invoice Number is already taken.',
            'items.required'      => 'At least one item is required on the invoice.',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $qty    = (float) ($item['qty'] ?? 1);
                $rate   = (float) ($item['rate'] ?? 0);
                $amount = $qty * $rate;
                $subtotal += $amount;

                $itemsData[] = [
                    'item_name'   => $item['item_name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'qty'         => $qty,
                    'rate'        => $rate,
                    'amount'      => $amount,
                ];
            }

            $discount    = (float) ($request->discount ?? 0);
            $totalAmount = max(0, $subtotal - $discount);
            $paymentMade = (float) ($request->payment_made ?? 0);
            $balanceDue  = max(0, $totalAmount - $paymentMade);

            // Determine status
            if ($balanceDue <= 0 && $totalAmount > 0) {
                $status = 'paid';
            } elseif ($paymentMade > 0 && $balanceDue > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }

            $invoice = Invoice::create([
                'invoice_no'       => $request->invoice_no,
                'client_id'        => $request->client_id,
                'company_id'       => null,
                'bank_id'          => $request->bank_id,
                'bill_to'          => $request->bill_to,
                'invoice_date'     => $request->invoice_date,
                'due_date'         => $request->due_date,
                'terms'            => 'Due on Receipt',
                'currency'         => $request->currency ?? 'PKR',
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'total_amount'     => $totalAmount,
                'payment_made'     => $paymentMade,
                'balance_due'      => $balanceDue,
                'notes'            => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'status'           => $status,
                'created_by'       => Auth::id(),
            ]);

            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            // If initial payment was made at invoice creation, record corresponding transactions
            if ($paymentMade > 0) {
                $this->createPaymentTransactions(
                    $invoice,
                    $paymentMade,
                    $request->invoice_date ?: Carbon::now()->format('Y-m-d'),
                    $request->bank_id ? (int) $request->bank_id : null,
                    $request->bank_id ? 'bank_transfer' : 'cash',
                    $request->invoice_no,
                    'Initial Payment recorded at invoice creation'
                );
            }

            if (function_exists('logUserActivity')) {
                logUserActivity('Invoice Created', "Invoice #{$invoice->invoice_no} created for Client ID {$invoice->client_id}", $invoice->id, 'Invoice');
            }

            DB::commit();

            return redirect()->route('invoice.show', $invoice->id)
                ->with('success', "Invoice #{$invoice->invoice_no} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('invoice_view');
        $invoice = Invoice::with(['client', 'items', 'creator', 'bank'])->findOrFail($id);
        $company = Company::first();
        $banks   = Bank::where('status', 'active')->orderBy('name')->get();

        $payments = ExpenseTransaction::with('bank')
            ->where(function ($q) use ($invoice) {
                $q->where('reference_no', $invoice->invoice_no)
                  ->orWhere('description', 'like', "%#{$invoice->invoice_no}%");
            })
            ->where('payee_type', 'client')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('invoice.show', compact('invoice', 'company', 'banks', 'payments'));
    }

    public function pdf($id)
    {
        $this->authorize('invoice_view');
        $invoice = Invoice::with(['client', 'items', 'bank'])->findOrFail($id);
        $company = Company::first();

        $pdf = Pdf::loadView('invoice.pdf', compact('invoice', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($invoice->invoice_no . '.pdf');
    }

    public function edit($id)
    {
        $this->authorize('invoice_edit');
        $invoice = Invoice::with(['items', 'bank'])->findOrFail($id);
        $clients = Client::orderBy('name')->get();

        $banks = Bank::where('status', 'active')
            ->orWhere('id', $invoice->bank_id)
            ->orderBy('name')
            ->get();

        $currencies = [
            'PKR' => 'PKR (Pakistani Rupee)',
            'SAR' => 'SAR (Saudi Riyal)',
            'USD' => 'USD (US Dollar)',
            'GBP' => 'GBP (British Pound)',
            'AED' => 'AED (UAE Dirham)',
        ];

        return view('invoice.edit', compact('invoice', 'clients', 'banks', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('invoice_edit');
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'bank_id'          => 'nullable|exists:banks,id',
            'invoice_no'       => 'required|string|max:50|unique:invoices,invoice_no,' . $invoice->id,
            'invoice_date'     => 'required|date',
            'due_date'         => 'required|date',
            'currency'         => 'required|string|max:10',
            'bill_to'          => 'required|string',
            'items'            => 'required|array|min:1',
            'items.*.rate'     => 'required|numeric|min:0',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'discount'         => 'nullable|numeric|min:0',
            'payment_made'     => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $qty    = (float) ($item['qty'] ?? 1);
                $rate   = (float) ($item['rate'] ?? 0);
                $amount = $qty * $rate;
                $subtotal += $amount;

                $itemsData[] = [
                    'item_name'   => $item['item_name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'qty'         => $qty,
                    'rate'        => $rate,
                    'amount'      => $amount,
                ];
            }

            $discount    = (float) ($request->discount ?? 0);
            $totalAmount = max(0, $subtotal - $discount);
            $paymentMade = (float) ($request->payment_made ?? 0);
            $balanceDue  = max(0, $totalAmount - $paymentMade);

            if ($balanceDue <= 0 && $totalAmount > 0) {
                $status = 'paid';
            } elseif ($paymentMade > 0 && $balanceDue > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }

            $invoice->update([
                'invoice_no'       => $request->invoice_no,
                'client_id'        => $request->client_id,
                'bank_id'          => $request->bank_id,
                'bill_to'          => $request->bill_to,
                'invoice_date'     => $request->invoice_date,
                'due_date'         => $request->due_date,
                'currency'         => $request->currency ?? 'PKR',
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'total_amount'     => $totalAmount,
                'payment_made'     => $paymentMade,
                'balance_due'      => $balanceDue,
                'notes'            => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'status'           => $status,
            ]);

            // Recreate items
            $invoice->items()->delete();
            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            if (function_exists('logUserActivity')) {
                logUserActivity('Invoice Updated', "Invoice #{$invoice->invoice_no} updated", $invoice->id, 'Invoice');
            }

            DB::commit();

            return redirect()->route('invoice.show', $invoice->id)
                ->with('success', "Invoice #{$invoice->invoice_no} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function recordPayment(Request $request, $id)
    {
        $this->authorize('invoice_payment');
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'bank_id'        => 'nullable|exists:banks,id',
            'payment_type'   => 'nullable|string',
            'reference_no'   => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        $invoice = Invoice::with(['client'])->findOrFail($id);
        $amount  = (float) $request->payment_amount;

        if ($amount > $invoice->balance_due) {
            return back()->withInput()->withErrors([
                'payment_amount' => 'Payment amount cannot exceed the balance due of ' . $invoice->currency . ' ' . number_format($invoice->balance_due, 2)
            ]);
        }

        DB::beginTransaction();
        try {
            $newPaymentMade = $invoice->payment_made + $amount;
            $balanceDue     = max(0, $invoice->total_amount - $newPaymentMade);

            if ($balanceDue <= 0 && $invoice->total_amount > 0) {
                $status = 'paid';
            } elseif ($newPaymentMade > 0 && $balanceDue > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }

            $updateData = [
                'payment_made' => $newPaymentMade,
                'balance_due'  => $balanceDue,
                'status'       => $status,
            ];

            if ($request->has('bank_id')) {
                $updateData['bank_id'] = $request->bank_id ?: null;
            }

            $invoice->update($updateData);

            // Record into ExpenseTransaction (Hits Bank Balance & Bank Statement) and Transaction (Client Ledger)
            $this->createPaymentTransactions(
                $invoice,
                $amount,
                $request->payment_date ?: Carbon::now()->format('Y-m-d'),
                $request->bank_id ? (int) $request->bank_id : null,
                $request->payment_type,
                $request->reference_no,
                $request->notes
            );

            if (function_exists('logUserActivity')) {
                $bankName = $request->bank_id ? (Bank::find($request->bank_id)?->name ?? 'Bank') : 'Cash';
                logUserActivity(
                    'Client Payment Recorded',
                    "Received {$invoice->currency} " . number_format($amount, 2) . " on {$request->payment_date} for Invoice #{$invoice->invoice_no} into {$bankName}",
                    $invoice->id,
                    'Invoice'
                );
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Payment of {$invoice->currency} " . number_format($amount, 2) . " successfully recorded on {$request->payment_date} for Invoice #{$invoice->invoice_no}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Create ledger and bank transactions for client invoice payments
     */
    protected function createPaymentTransactions(Invoice $invoice, float $amount, string $paymentDate, ?int $bankId, ?string $paymentType, ?string $referenceNo, ?string $notes)
    {
        if ($amount <= 0) {
            return null;
        }

        $hajjUmrah = session('dashboard_package', 'hajj');
        if (!in_array($hajjUmrah, ['hajj', 'umrah'])) {
            $hajjUmrah = 'hajj';
        }
        $year       = (int) Carbon::parse($paymentDate)->year;
        $payType    = $paymentType ?: ($bankId ? 'bank' : 'cash');
        $clientName = $invoice->client->name ?? '';

        // 1. Create ExpenseTransaction (Hits Bank Live Balance, Bank Ledger & Transaction Reports)
        $expenseTrans = ExpenseTransaction::create([
            'bank_id'          => $bankId ?: null,
            'client_id'        => $invoice->client_id ?: null,
            'company_id'       => $invoice->company_id ?: null,
            'hajj_umrah'       => $hajjUmrah,
            'year'             => $year,
            'amount'           => $amount,
            'payment_type'     => ($payType === 'cash') ? 'cash' : 'bank',
            'transaction_type' => 'credit',
            'payee_type'       => 'client',
            'currency'         => $invoice->currency ?: 'PKR',
            'exchange_rate'    => 1,
            'date'             => Carbon::parse($paymentDate)->format('Y-m-d'),
            'reference_no'     => $referenceNo ?: $invoice->invoice_no,
            'description'      => "Client Payment for Invoice #{$invoice->invoice_no}" . ($clientName ? " ({$clientName})" : "") . ($notes ? " | {$notes}" : ""),
        ]);

        // 2. Create Transaction (Reflects in Client Ledger)
        $booking = null;
        if ($invoice->client_id) {
            $booking = Booking::where('client_id', $invoice->client_id)->latest('id')->first();
        }

        $txnPaymentType = ($payType === 'cash') ? 'cash' : (($payType === 'cheque') ? 'cheque' : 'bank_transfer');

        Transaction::create([
            'client_id'        => $invoice->client_id,
            'company_id'       => $invoice->company_id,
            'booking_id'       => $booking ? $booking->id : null,
            'payment_type'     => $txnPaymentType,
            'amount'           => $amount,
            'payment_date'     => Carbon::parse($paymentDate)->format('Y-m-d'),
            'reference_number' => $referenceNo ?: $invoice->invoice_no,
            'notes'            => "Payment for Invoice #{$invoice->invoice_no}" . ($notes ? " - {$notes}" : ""),
            'status'           => 'confirmed',
        ]);

        // 3. If booking exists, update balance
        if ($booking) {
            $newReceived = (float) $booking->total_received + $amount;
            $booking->update([
                'total_received' => $newReceived,
                'balance'        => max(0, (float) $booking->total_amount - $newReceived),
            ]);
        }

        return $expenseTrans;
    }

    public function destroy($id)
    {
        $this->authorize('invoice_trash');
        $invoice   = Invoice::findOrFail($id);
        $invoiceNo = $invoice->invoice_no;

        // Clean up linked transactions
        ExpenseTransaction::where('reference_no', $invoiceNo)
            ->orWhere('description', 'like', "%Invoice #{$invoiceNo}%")
            ->delete();

        Transaction::where('reference_number', $invoiceNo)
            ->orWhere('notes', 'like', "%Invoice #{$invoiceNo}%")
            ->delete();

        $invoice->delete();

        if (function_exists('logUserActivity')) {
            logUserActivity('Invoice Deleted', "Invoice #{$invoiceNo} deleted", $invoice->id, 'Invoice');
        }

        return redirect()->route('invoice.index')
            ->with('success', "Invoice #{$invoiceNo} deleted successfully.");
    }
}
