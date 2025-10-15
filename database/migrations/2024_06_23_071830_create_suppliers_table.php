<?php

use App\Enums\SupplierTypeEnum;
use App\Models\City;
use App\Models\Supplier;
use App\Models\User;
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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('link')->nullable();
            $table->string('bank_number')->nullable();
            $table->tinyInteger('type')->default(SupplierTypeEnum::PHYSICAL->value);
            $table->string('email')->nullable();
            $table->foreignIdFor(Supplier::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(City::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
