<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_id',
        'bank_id',
        'client_id',
        'company_id',
        'hajj_umrah',        
        'year',              
        'amount',
        'payment_type',
        'transaction_type',
        'payee_type',
        'reference_no',
        'proof',
        'date',
        'description',
        'currency',
        'exchange_rate',
    ];

    protected $casts = [
        'date'          => 'date',
        'amount'        => 'decimal:2',
        'exchange_rate' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'reference_no', 'invoice_no');
    }
}
