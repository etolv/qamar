<?php

use App\Enums\RateTypeEnum;
use App\Models\Booking;
use App\Models\RateReason;
use App\Models\RateType;
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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RateReason::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->morphs('model');
            $table->tinyInteger('type');
            $table->integer('rate');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
