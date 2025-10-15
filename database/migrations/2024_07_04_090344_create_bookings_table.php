<?php

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Driver;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->datetime('date');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->double('total')->nullable();
            $table->double('tax')->nullable();
            $table->double('grand_total')->nullable();
            $table->double('discount')->default(0.00);
            $table->tinyInteger('status')->default(StatusEnum::PENDING->value);
            $table->tinyInteger('payment_status')->default(PaymentStatusEnum::PENDING->value);
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Coupon::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreignId('driver_id')->constrained()->references('id')->on('employees')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
