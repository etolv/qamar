<?php

use App\Enums\ShiftTypeEnum;
use App\Enums\WeekDaysEnum;
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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('daily_hours')->default(0);
            $table->time('start_time');
            $table->time('end_time');
            $table->time('start_break')->nullable();
            $table->time('end_break')->nullable();
            $table->tinyInteger('type')->default(ShiftTypeEnum::MORNING->value);
            $table->tinyInteger('holiday')->default(WeekDaysEnum::FRIDAY->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
