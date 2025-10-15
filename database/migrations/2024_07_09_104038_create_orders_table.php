<?php

use App\Enums\DepartmentEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Customer;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->datetime('date');
            $table->text('description')->nullable();
            $table->double('total')->nullable();
            $table->double('tax')->default(0);
            $table->double('grand_total')->nullable();
            $table->double('discount')->default(0.00);
            $table->tinyInteger('status')->default(StatusEnum::PENDING->value);
            $table->boolean('is_gift')->default(false);
            $table->boolean('is_mobile')->default(false);
            $table->boolean('tax_included')->default(true);
            $table->tinyInteger('department')->default(DepartmentEnum::SALON->value);
            $table->tinyInteger('payment_status')->default(PaymentStatusEnum::PENDING->value);
            $table->tinyInteger('payment_type')->default(PaymentTypeEnum::CASH->value);
            $table->foreignId('gifter_id')->nullable()->constrained()->references('id')->on('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('gift_end_date')->nullable();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Branch::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Coupon::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
