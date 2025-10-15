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
        Schema::table('order_service_returns', function (Blueprint $table) {
            $table->double('total')->default(0.00);
            $table->double('tax')->default(0.00);
            $table->double('grand_total')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_service_returns', function (Blueprint $table) {
            $table->dropColumn('total');
            $table->dropColumn('tax');
            $table->dropColumn('grand_total');
        });
    }
};
