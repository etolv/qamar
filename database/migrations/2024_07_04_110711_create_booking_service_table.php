<?php

use App\Enums\ServiceStatusEnum;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
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
        Schema::create('booking_service', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Booking::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status')->default(ServiceStatusEnum::PENDING->value);
            $table->integer('quantity')->default(1);
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_service');
    }
};
