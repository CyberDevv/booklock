<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('screening__seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->char('row_label', 1);
            $table->unsignedInteger('seat_number');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            $table->unique(['screening_id', 'row_label', 'seat_number']);
            $table->index(['screening_id', 'is_booked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening__seats');
    }
};
