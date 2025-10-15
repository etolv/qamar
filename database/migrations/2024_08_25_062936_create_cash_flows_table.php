<?php

use App\Enums\CashFlowStatusEnum;
use App\Enums\CashFlowTypeEnum;
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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->float('amount', 10, 2);
            $table->morphs('flowable');
            $table->tinyInteger('type')->default(CashFlowTypeEnum::EXPENSE->value);
            $table->tinyInteger('status')->default(CashFlowStatusEnum::PAID->value);
            $table->text('reason')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('split_months_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
