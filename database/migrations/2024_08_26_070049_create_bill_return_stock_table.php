<?php

use App\Models\BillReturn;
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
        Schema::create('bill_return_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BillReturn::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity');
            $table->float('price')->default(0);
            $table->float('tax')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_return_stock');
    }
};
