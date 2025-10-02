<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'route_id',
        'booking_number',
        'passenger_name',
        'phone',
        'travel_date',
        'seats',
        'amount',
        'status',
        'qr_code',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public static function generateBookingNumber()
    {
        do {
            $number = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }
}
