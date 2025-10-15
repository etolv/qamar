<?php

use App\Enums\ProfitTypeEnum;
use App\Enums\WeekDaysEnum;
use App\Models\Branch;
use App\Models\City;
use App\Models\Job;
use App\Models\Nationality;
use App\Models\User;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(City::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Branch::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Job::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Nationality::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('holiday')->default(WeekDaysEnum::FRIDAY->value);
            $table->tinyInteger('profit_type')->default(ProfitTypeEnum::SERVICE->value);
            $table->date('start_work')->nullable();
            $table->date('birthday')->nullable();
            $table->string('residence_number')->nullable();
            $table->date('residence_expiration')->nullable();
            $table->string('insurance_company')->nullable();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiration')->nullable();
            $table->date('insurance_card_expiration')->nullable();
            $table->double('vacation_days')->default(21);
            $table->double('used_vacation_days')->default(0);
            $table->double('remaining_vacation_days')->default(21);
            $table->string('employee_no')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
