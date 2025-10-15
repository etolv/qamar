<?php

use App\Enums\TaxTypeEnum;
use App\Models\Bill;
use App\Models\Product;
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
        Schema::create('bill_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bill::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('purchase_unit_id')->constrained()->references('id')->on('units')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('retail_unit_id')->constrained()->references('id')->on('units')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('expiration_date')->nullable();
            $table->float('purchase_price')->default(0);
            $table->float('tax')->default(0);
            $table->float('sell_price')->default(0);
            $table->float('exchange_price')->default(0);
            $table->float('profit_percentage')->default(0);
            $table->string('barcode')->nullable();
            $table->integer('quantity');
            $table->integer('convert')->default(1);
            $table->tinyInteger('tax_type')->default(TaxTypeEnum::TAXED->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_product');
    }
};
