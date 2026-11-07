<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'account_name',
        'account_number',
        'branch',
        'status',
    ];

    /**
     * Invoices linked to this bank
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'bank_id');
    }

    /**
     * Expense transactions hitting this bank (Credits & Debits)
     */
    public function expenseTransactions()
    {
        return $this->hasMany(ExpenseTransaction::class, 'bank_id');
    }

    /**
     * Live Bank Balance = Credits - Debits from transactions
     */
    public function getBalanceAttribute(): float
    {
        $credits = (float) $this->expenseTransactions()
            ->where('transaction_type', 'credit')
            ->sum('amount');

        $debits = (float) $this->expenseTransactions()
            ->where('transaction_type', 'debit')
            ->sum('amount');

        return $credits - $debits;
    }
}
