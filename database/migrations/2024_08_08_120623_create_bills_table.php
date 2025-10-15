<?php

use App\Enums\BillTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\TaxTypeEnum;
use App\Models\BillType;
use App\Models\Branch;
use App\Models\Supplier;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('term')->nullable();
            $table->tinyInteger('department')->default(DepartmentEnum::SALON->value);
            $table->foreignIdFor(Supplier::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(BillType::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Branch::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('type')->default(BillTypeEnum::PURCHASE->value);
            $table->tinyInteger('tax_type')->default(TaxTypeEnum::TAXED->value);
            $table->double('total')->default(0.00);
            $table->double('grand_total')->default(0.00);
            $table->double('tax')->default(0.00);
            $table->double('paid')->default(0.00);
            $table->boolean('received')->default(1);
            $table->datetime('receiving_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
