<?php

use App\Enums\AttendanceStatusEnum;
use App\Enums\OverTimeStatusEnum;
use App\Models\Employee;
use App\Models\Shift;
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
        Schema::create('temp_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Shift::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('date')->useCurrent();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->float('total', 8, 2)->default(0);
            $table->float('missing_hours')->default(0);
            $table->float('extra_hours')->default(0);
            $table->tinyInteger('overtime_status')->default(OverTimeStatusEnum::PENDING->value);
            $table->tinyInteger('status')->default(AttendanceStatusEnum::NORMAL->value);
            $table->boolean('is_holiday')->default(false);
            $table->boolean('on_vacation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_attendances');
    }
};
