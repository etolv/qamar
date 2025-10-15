<?php

use App\Enums\TaskStatusEnum;
use App\Models\Employee;
use App\Models\Task;
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
        Schema::create('employee_task', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status')->default(TaskStatusEnum::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_task');
    }
};
