<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_no',
        'client_id',
        'company_id',
        'bank_id',
        'bill_to',
        'invoice_date',
        'due_date',
        'terms',
        'currency',
        'subtotal',
        'discount',
        'total_amount',
        'payment_made',
        'balance_due',
        'notes',
        'terms_conditions',
        'status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_made' => 'decimal:2',
        'balance_due'  => 'decimal:2',
    ];

    /**
     * Generate next sequential invoice number (e.g. INV-004492).
     */
    public static function nextInvoiceNo(): string
    {
        $last = static::withTrashed()->orderBy('id', 'desc')->first();
        if ($last && preg_match('/(\d+)/', $last->invoice_no, $matches)) {
            $nextNum = (int) $matches[1] + 1;
        } else {
            $nextNum = 4492;
        }
        return 'INV-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenseTransactions()
    {
        return $this->hasMany(ExpenseTransaction::class, 'reference_no', 'invoice_no');
    }
}
