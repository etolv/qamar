<?php

use App\Enums\DepartmentEnum;
use App\Models\Branch;
use App\Models\Product;
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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('barcode');
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Unit::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity');
            $table->float('price');
            $table->tinyInteger('department')->default(DepartmentEnum::SALON->value);
            $table->date('expiration_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
