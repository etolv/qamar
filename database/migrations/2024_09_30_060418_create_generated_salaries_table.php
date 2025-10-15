<?php

use App\Models\Employee;
use App\Models\Salary;
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
        Schema::create('generated_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Salary::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('month');
            $table->double('target_total')->default(0.00); // target employee reached
            $table->double('target')->default(0.00); // not showed
            $table->double('working_hours')->default(0.00);
            $table->double('extra_hours')->default(0.00);
            // $table->double('compensation_hours')->default(0.00);
            $table->double('missing_hours')->default(0.00);
            $table->double('rounded_hours')->default(0.00); // not applied
            $table->double('profit_percentage')->default(0.00);
            $table->double('base_salary')->default(0.00);
            $table->double('deduction')->default(0.00);
            $table->double('advance')->default(0.00);
            $table->double('gift')->default(0.00);
            $table->double('overtime')->default(0.00);
            $table->double('profit_total')->default(0.00);
            $table->double('total_deduction')->default(0.00);
            $table->double('total_extra')->default(0.00);
            $table->double('total')->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_salaries');
    }
};
