<?php

use App\Models\Brand;
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
        Schema::create('brand_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->foreignIdFor(Brand::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->unique(['brand_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_translations');
    }
};
