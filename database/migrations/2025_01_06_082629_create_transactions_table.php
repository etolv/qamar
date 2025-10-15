<?php

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description', 1000)->nullable();
            $table->nullableMorphs('model');
            $table->decimal('amount', 15, 2);
            // the from account will be the debtor
            $table->foreignId('from_account_id')->constrained()->references('id')->on('accounts')->cascadeOnDelete()->cascadeOnUpdate();
            // the to account will be the creditor
            $table->foreignId('to_account_id')->constrained()->references('id')->on('accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_automatic')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
