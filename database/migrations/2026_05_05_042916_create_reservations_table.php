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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('status', [
                'pending',    // lock seat (temporary)
                'confirmed',  // paid
                'expired',    // auto release
                'cancelled'   // manual cancel
            ]);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
