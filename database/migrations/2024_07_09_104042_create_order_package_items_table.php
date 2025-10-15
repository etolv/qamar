<?php

use App\Enums\ServiceStatusEnum;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\Package;
use App\Models\PackageItem;
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
        Schema::create('order_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_package_id')->constrained()->references('id')->on('order_package')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(PackageItem::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('price');
            $table->integer('quantity')->default(1);
            $table->tinyInteger('status')->default(ServiceStatusEnum::PENDING->value);
            $table->integer('session_count')->default(1);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_package_items');
    }
};
