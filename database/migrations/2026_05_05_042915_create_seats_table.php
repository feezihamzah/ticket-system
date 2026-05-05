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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('seat_number'); 
            $table->enum('status', ['available', 'reserved', 'sold'])
                ->default('available');
            $table->timestamps();
            $table->unique(['event_id', 'seat_number']);
            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
