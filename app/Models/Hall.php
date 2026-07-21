<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Hall extends Model
{
    /** @use HasFactory<\Database\Factories\HallFactory> */
    use HasFactory;

    // protected $fillable = [
    //     'name',
    //     'rows',
    //     'seats_per_row'
    // ];

    public function totalSeats(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->rows * $this->seats_per_row
        );
    }
}
