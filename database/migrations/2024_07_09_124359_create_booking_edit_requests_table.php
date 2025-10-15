<?php

use App\Models\Booking;
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
        Schema::create('booking_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Booking::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('type')->default(1);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_edit_requests');
    }
};
