<?php

use App\Enums\ItemTypeEnum;
use App\Enums\ServiceStatusEnum;
use App\Models\Employee;
use App\Models\Order;
use App\Models\PackageItem;
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
        Schema::create('order_service', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('order_package_id')->nullable()->constrained()->references('id')->on('order_package')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(PackageItem::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status')->default(ServiceStatusEnum::PENDING->value);
            $table->tinyInteger('type')->default(ItemTypeEnum::NORMAL->value);
            $table->integer('quantity')->default(1);
            $table->integer('session_count')->default(1);
            $table->date('due_date')->nullable();
            $table->double('session_price')->default(0.00);
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_service');
    }
};
