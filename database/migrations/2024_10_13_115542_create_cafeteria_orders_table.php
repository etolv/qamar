<?php

use App\Enums\DepartmentEnum;
use App\Enums\OrderableTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use App\Models\Branch;
use App\Models\Customer;
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
        Schema::create('cafeteria_orders', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->double('total')->nullable();
            $table->double('tax')->default(0);
            $table->boolean('tax_included')->default(true);
            $table->double('grand_total')->nullable();
            $table->double('discount')->default(0.00);
            $table->tinyInteger('status')->default(StatusEnum::PENDING->value);
            $table->tinyInteger('payment_status')->default(PaymentStatusEnum::PENDING->value);
            $table->tinyInteger('type')->default(OrderableTypeEnum::CAFETERIA->value);
            $table->nullableMorphs('orderable');
            $table->foreignIdFor(Branch::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafeteria_orders');
    }
};
