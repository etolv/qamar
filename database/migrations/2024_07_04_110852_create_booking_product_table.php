<?php

use App\Enums\ItemTypeEnum;
use App\Models\Booking;
use App\Models\Product;
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
        Schema::create('booking_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Booking::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('booking_service_id')->nullable()->constrained()->references('id')->on('booking_service')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('booking_product');
    }
};
