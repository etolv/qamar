<?php

use App\Enums\DepartmentEnum;
use App\Enums\StockWithdrawalTypeEnum;
use App\Enums\WithdrawalTypeEnum;
use App\Models\Employee;
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
        Schema::create('stock_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Stock::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('type')->default(StockWithdrawalTypeEnum::CONSUMPTION->value);
            $table->tinyInteger('department')->default(DepartmentEnum::SALON->value);
            $table->integer('quantity')->default(1);
            $table->float('price')->default(0);
            $table->float('tax')->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_withdrawals');
    }
};
