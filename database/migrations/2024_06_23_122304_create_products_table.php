<?php

use App\Enums\DepartmentEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('name');
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Brand::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('department')->default(DepartmentEnum::SALON->value);
            // $table->foreignIdFor(Unit::class, 'sale_unit_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreignIdFor(Unit::class, 'buy_unit_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            // $table->float('sale_price');
            // $table->float('exchange_price');
            $table->integer('min_quantity')->default(0);
            $table->tinyInteger('consumption_type');
            $table->boolean('refundable')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
