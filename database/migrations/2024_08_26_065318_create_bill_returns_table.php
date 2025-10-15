<?php

use App\Models\Bill;
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
        Schema::create('bill_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supplier::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Bill::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('total')->default(0.00);
            $table->double('tax')->default(0.00);
            $table->double('grand_total')->default(0.00);
            $table->double('returned')->default(0.00);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_returns');
    }
};
