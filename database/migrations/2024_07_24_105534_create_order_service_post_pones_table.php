<?php

use App\Enums\ServiceStatusEnum;
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
        Schema::create('order_service_post_pones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_service_id')->constrained()->references('id')->on('order_service')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->tinyInteger('status')->default(ServiceStatusEnum::PENDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_service_post_pones');
    }
};
