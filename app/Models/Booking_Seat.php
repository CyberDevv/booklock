<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Booking_Seat extends Model
{
    /** @use HasFactory<\Database\Factories\BookingSeatFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'screening_seat_id',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function screeningSeat(): BelongsTo
    {
        return $this->belongsTo(Screening_Seat::class);
    }
}
