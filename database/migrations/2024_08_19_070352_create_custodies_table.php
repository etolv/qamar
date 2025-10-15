<?php

use App\Enums\CustodyStatusEnum;
use App\Models\Employee;
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
        Schema::create('custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(1);
            $table->float('price')->default(0);
            $table->tinyInteger('status')->default(CustodyStatusEnum::USING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custodies');
    }
};
