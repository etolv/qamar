<?php

use App\Enums\TripStatusEnum;
use App\Models\Driver;
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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Driver::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->nullableMorphs('tripable');
            $table->double('from_lng')->nullable();
            $table->double('from_lat')->nullable();
            $table->double('to_lng')->nullable();
            $table->double('to_lat')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->tinyInteger('status')->default(TripStatusEnum::PENDING->value);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
