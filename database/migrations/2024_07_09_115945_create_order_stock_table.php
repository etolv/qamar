<?php

use App\Enums\ItemTypeEnum;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\PackageItem;
use App\Models\Service;
use App\Models\Stock;
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
        Schema::create('order_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('order_package_id')->nullable()->constrained()->references('id')->on('order_package')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(PackageItem::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('order_service_id')->nullable()->constrained()->references('id')->on('order_service')->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('type')->default(ItemTypeEnum::NORMAL->value);
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
        Schema::dropIfExists('order_stock');
    }
};
