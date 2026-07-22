<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Screening extends Model
{
    /** @use HasFactory<\Database\Factories\ScreeningFactory> */
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'hall_id',
        'starts_at',
        'price_kobo'
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'price_kobo' => 'integer'
        ];
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function screeningSeats(): HasMany
    {
        return $this->hasMany(Screening_Seat::class);
    }


}
