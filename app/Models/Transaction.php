<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'transaction_number',
        'amount',
        'payment_method',
        'receipt_image',
        'payment_status',
        'rejection_count',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateTransactionNumber()
    {
        do {
            $number = 'TXN' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('transaction_number', $number)->exists());

        return $number;
    }
}
