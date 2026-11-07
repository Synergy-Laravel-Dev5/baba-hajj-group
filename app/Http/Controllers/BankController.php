<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Bank;
use App\Models\ExpenseTransaction;
use Carbon\Carbon;

class BankController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('bank_view');

        $allBanks = Bank::with('expenseTransactions')->latest()->get();
        $totalBanks = $allBanks->count();
        $activeBanks = $allBanks->where('status', 'active')->count();
        $inactiveBanks = $allBanks->where('status', 'inactive')->count();
        $totalLiquidity = $allBanks->where('status', 'active')->sum(function ($b) {
            return $b->balance;
        });

        $query = Bank::with('expenseTransactions');

        $status = $request->query('status', 'all');
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $search = $request->query('search', '');
        if (!empty($search)) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('account_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhere('branch', 'like', "%{$s}%");
            });
        }

        $banks = $query->latest()->get();

        return view('bank.index', compact('banks', 'totalBanks', 'activeBanks', 'inactiveBanks', 'totalLiquidity', 'status', 'search'));
    }

    public function create()
    {
        $this->authorize('bank_create');
        return view('bank.create');
    }

    public function store(Request $request)
    {
        $this->authorize('bank_create');
        $request->validate([
            'name'           => 'required|string|max:255',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive',
        ]);
        
        $bank = Bank::create([
            'name'           => $request->name,
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
            'branch'         => $request->branch,
            'status'         => $request->status ?? 'active',
        ]);

        if (function_exists('logUserActivity')) {
            logUserActivity('Bank Created', "Bank '{$bank->name}' created.", $bank->id, 'Bank');
        }

        return redirect()->route('bank.index')->with('success', "Bank '{$bank->name}' created successfully.");
    }

    public function edit(Bank $bank)
    {
        $this->authorize('bank_edit');
        return view('bank.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $this->authorize('bank_edit');
        $request->validate([
            'name'           => 'required|string|max:255',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'branch'         => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive',
        ]);
        
        $bank->update([
            'name'           => $request->name,
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
            'branch'         => $request->branch,
            'status'         => $request->status,
        ]);

        if (function_exists('logUserActivity')) {
            logUserActivity('Bank Updated', "Bank '{$bank->name}' updated.", $bank->id, 'Bank');
        }

        return redirect()->route('bank.index')->with('success', "Bank '{$bank->name}' updated successfully.");
    }

    public function toggleStatus(Bank $bank)
    {
        $this->authorize('bank_edit');
        $newStatus = ($bank->status === 'active') ? 'inactive' : 'active';
        $bank->update(['status' => $newStatus]);

        if (function_exists('logUserActivity')) {
            logUserActivity('Bank Status Changed', "Bank '{$bank->name}' status changed to '{$newStatus}'.", $bank->id, 'Bank');
        }

        return redirect()->route('bank.index')->with('success', "Bank '{$bank->name}' status set to " . ucfirst($newStatus) . ".");
    }

    public function destroy(Bank $bank)
    {
        $this->authorize('bank_trash');
        $bankName = $bank->name;
        if (function_exists('logUserActivity')) {
            logUserActivity('Bank Deleted', "Bank '{$bankName}' deleted.", $bank->id, 'Bank');
        }
        $bank->delete();

        return redirect()->route('bank.index')->with('success', "Bank '{$bankName}' deleted successfully.");
    }

    /**
     * Bank Ledger / Statement
     */
    public function ledger(Request $request, Bank $bank)
    {
        $this->authorize('bank_view');
        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');

        // Fetch all transactions hitting this bank
        $expQuery = ExpenseTransaction::with(['client', 'expense', 'company'])
            ->where('bank_id', $bank->id);

        if ($fromDate) {
            $expQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $expQuery->whereDate('date', '<=', $toDate);
        }
        $expenseTxns = $expQuery->get();

        // Build ledger entries
        $entries = collect();

        foreach ($expenseTxns as $txn) {
            $isCredit = ($txn->transaction_type === 'credit');
            $party = $txn->client ? $txn->client->name : ($txn->expense ? $txn->expense->name : 'General');
            $entries->push((object) [
                'date'        => $txn->date,
                'created_at'  => $txn->created_at,
                'type'        => $isCredit ? 'Credit (In)' : 'Debit (Out)',
                'category'    => $txn->payee_type ? ucfirst($txn->payee_type) : ($isCredit ? 'Client Receipt' : 'Expense'),
                'party'       => $party,
                'reference'   => $txn->reference_no,
                'description' => $txn->description,
                'credit'      => $isCredit ? (float) $txn->amount : 0,
                'debit'       => !$isCredit ? (float) $txn->amount : 0,
                'currency'    => $txn->currency ?? 'PKR',
            ]);
        }

        // Sort chronologically ascending to compute running balance
        $sortedEntries = $entries->sortBy(function ($item) {
            return $item->date ? Carbon::parse($item->date)->timestamp : 0;
        })->values();

        $running = 0;
        $totalCredits = 0;
        $totalDebits = 0;

        foreach ($sortedEntries as $item) {
            $totalCredits += $item->credit;
            $totalDebits  += $item->debit;
            $running      += ($item->credit - $item->debit);
            $item->running_balance = $running;
        }

        $allBanks = Bank::where('status', 'active')->orderBy('name')->get();

        return view('bank.ledger', compact('bank', 'sortedEntries', 'totalCredits', 'totalDebits', 'running', 'fromDate', 'toDate', 'allBanks'));
    }
}
