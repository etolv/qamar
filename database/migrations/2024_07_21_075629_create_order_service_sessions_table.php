<?php

use App\Enums\SessionStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Employee;
use App\Models\OrderService;
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
        Schema::create('order_service_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_service_id')->constrained()->references('id')->on('order_service')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('date');
            $table->tinyInteger('status')->default(SessionStatusEnum::PENDING->value);
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_service_sessions');
    }
};
