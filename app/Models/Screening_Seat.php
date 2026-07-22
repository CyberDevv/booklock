<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screening_Seat extends Model
{
    /** @use HasFactory<\Database\Factories\Screening_SeatFactory> */
    use HasFactory;

    protected $fillable = [
        'screening_id',
        'row_label',
        'seat_number',
        'is_booked'
    ];

    protected function casts(): array
    {
        return [
            'is_booked' => 'boolean',
            'seat_number' => 'integer'
        ];
    }

    public function screening(): BelongsTo
    {
        return $this->belongsTo(Screening::class);
    }
}
