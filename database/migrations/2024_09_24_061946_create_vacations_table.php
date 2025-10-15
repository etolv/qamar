<?php

use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Employee;
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
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_hourly')->default(false);
            $table->tinyInteger('hours')->nullable();
            $table->time('from_hour')->nullable();
            $table->time('to_hour')->nullable();
            $table->text('reason')->nullable();
            $table->tinyInteger('type')->default(VacationTypeEnum::ANNUAL->value);  // 'annual', 'sick', 'unpaid', 'public_holiday'.
            $table->tinyInteger('status')->default(VacationStatusEnum::IN_REVIEW->value);  // 'approved', 'declined', 'pending_report', 'in_review', canceled'.
            $table->double('days')->default(1);
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
