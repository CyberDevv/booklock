<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    /** @use HasFactory<\Database\Factories\HallFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'rows',
        'seats_per_row'
    ];

    protected function casts(): array
    {
        return [
            'rows' => 'integer',
            'seats_per_row' => 'integer'
        ];
    }

    public function Screenings(): HasMany
    {
        return $this->hasMany(Screening::class);
    }

    public function totalSeats(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->rows * $this->seats_per_row
        );
    }
}
