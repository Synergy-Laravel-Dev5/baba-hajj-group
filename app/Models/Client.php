<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'company_id',
        'company_name',
        'passport_number',
        'cnic',
        'phone',
        'email',
        'type',
        'status',
        'package_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function referral()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedCnicAttribute(): ?string
    {
        if (empty($this->cnic)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $this->cnic);
        if (strlen($digits) === 13) {
            return substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12, 1);
        }
        return $this->cnic;
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $this->phone);
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '92' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '92') && strlen($digits) === 10) {
            $digits = '92' . $digits;
        }
        if (str_starts_with($digits, '92') && strlen($digits) === 12) {
            return '+92 ' . substr($digits, 2, 3) . ' ' . substr($digits, 5);
        }
        return $this->phone;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $this->phone);
        if (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '92') && strlen($digits) === 10) {
            $digits = '92' . $digits;
        }
        return !empty($digits) ? 'https://wa.me/' . $digits : null;
    }
}
