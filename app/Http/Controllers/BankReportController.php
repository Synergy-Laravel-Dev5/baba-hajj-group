<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Bank;
use App\Models\Invoice;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BankReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the filter form for the Client Invoices Bank Report.
     */
    public function reportFilter()
    {
        $this->authorize('bank_report_view');
        $banks   = Bank::where('status', 'active')->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        return view('bank_report.report_filter', compact('banks', 'clients'));
    }

    /**
     * Display the Client Invoices Bank Report:
     * Shows amount after discount (total_amount), paid amount (payment_made),
     * remaining balance due (balance_due), and bank reflection for each invoice.
     */
    public function report(Request $request)
    {
        $this->authorize('bank_report_view');

        $bankId   = $request->get('bank_id');
        $clientId = $request->get('client_id');
        $status   = $request->get('status');
        $fromDate = $request->get('from_date');
        $toDate   = $request->get('to_date');
        $search   = $request->get('search');

        $query = Invoice::with(['client', 'bank', 'expenseTransactions.bank']);

        if ($bankId) {
            $query->where(function ($q) use ($bankId) {
                $q->where('bank_id', $bankId)
                  ->orWhereHas('expenseTransactions', function ($eq) use ($bankId) {
                      $eq->where('bank_id', $bankId);
                  });
            });
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('invoice_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $query->where('invoice_date', '>=', $fromDate);
        } elseif ($toDate) {
            $query->where('invoice_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhere('bill_to', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('bank', function ($bq) use ($search) {
                      $bq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('account_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        // Totals: After discount, Paid, Remaining Balance (Baki)
        $totalInvoiced = (float) $invoices->sum('total_amount');
        $totalPaid     = (float) $invoices->sum('payment_made');
        $totalBalance  = (float) $invoices->sum('balance_due');
        $totalCount    = $invoices->count();

        $allBanks = Bank::where('status', 'active')->orderBy('name')->get();
        $clients  = Client::orderBy('name')->get();

        // Bank-wise summary breakdown matrix
        $bankBreakdown = [];
        foreach ($allBanks as $b) {
            $bankInvoices = $invoices->filter(function ($inv) use ($b) {
                return $inv->bank_id == $b->id || $inv->expenseTransactions->contains('bank_id', $b->id);
            });

            if ($bankInvoices->isNotEmpty()) {
                $bankBreakdown[] = [
                    'bank'           => $b,
                    'count'          => $bankInvoices->count(),
                    'total_invoiced' => (float) $bankInvoices->sum('total_amount'),
                    'total_paid'     => (float) $bankInvoices->sum('payment_made'),
                    'total_balance'  => (float) $bankInvoices->sum('balance_due'),
                ];
            }
        }

        // Unassigned invoices if any
        $unassigned = $invoices->filter(function ($inv) {
            return empty($inv->bank_id) && $inv->expenseTransactions->whereNotNull('bank_id')->isEmpty();
        });
        if ($unassigned->isNotEmpty()) {
            $bankBreakdown[] = [
                'bank'           => (object)[
                    'name'           => 'Unassigned / Cash',
                    'account_number' => 'N/A',
                    'branch'         => 'No Bank Linked'
                ],
                'count'          => $unassigned->count(),
                'total_invoiced' => (float) $unassigned->sum('total_amount'),
                'total_paid'     => (float) $unassigned->sum('payment_made'),
                'total_balance'  => (float) $unassigned->sum('balance_due'),
            ];
        }

        $selectedBank   = $bankId ? Bank::find($bankId) : null;
        $selectedClient = $clientId ? Client::find($clientId) : null;

        return view('bank_report.report', compact(
            'invoices',
            'allBanks',
            'clients',
            'totalInvoiced',
            'totalPaid',
            'totalBalance',
            'totalCount',
            'bankBreakdown',
            'bankId',
            'clientId',
            'status',
            'fromDate',
            'toDate',
            'search',
            'selectedBank',
            'selectedClient'
        ));
    }

    /**
     * Export the Client Invoices Bank Report to PDF.
     */
    public function reportPdf(Request $request)
    {
        $this->authorize('bank_report_view');

        $bankId   = $request->get('bank_id');
        $clientId = $request->get('client_id');
        $status   = $request->get('status');
        $fromDate = $request->get('from_date');
        $toDate   = $request->get('to_date');
        $search   = $request->get('search');

        $query = Invoice::with(['client', 'bank', 'expenseTransactions.bank']);

        if ($bankId) {
            $query->where(function ($q) use ($bankId) {
                $q->where('bank_id', $bankId)
                  ->orWhereHas('expenseTransactions', function ($eq) use ($bankId) {
                      $eq->where('bank_id', $bankId);
                  });
            });
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('invoice_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $query->where('invoice_date', '>=', $fromDate);
        } elseif ($toDate) {
            $query->where('invoice_date', '<=', $toDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhere('bill_to', 'LIKE', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('bank', function ($bq) use ($search) {
                      $bq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('account_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        $totalInvoiced = (float) $invoices->sum('total_amount');
        $totalPaid     = (float) $invoices->sum('payment_made');
        $totalBalance  = (float) $invoices->sum('balance_due');
        $totalCount    = $invoices->count();

        $selectedBank   = $bankId ? Bank::find($bankId) : null;
        $selectedClient = $clientId ? Client::find($clientId) : null;

        $pdf = Pdf::loadView('bank_report.report_pdf', compact(
            'invoices',
            'totalInvoiced',
            'totalPaid',
            'totalBalance',
            'totalCount',
            'fromDate',
            'toDate',
            'selectedBank',
            'selectedClient',
            'status'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('bank-invoices-report.pdf');
    }
}
