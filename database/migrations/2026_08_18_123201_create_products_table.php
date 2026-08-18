<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            // Money as integer minor units. A float would accumulate rounding
            // error the moment totals are summed; decimal is fine too, but
            // integers keep arithmetic exact everywhere including JavaScript.
            $table->unsignedInteger('price_cents');
            $table->unsignedInteger('stock')->default(0);
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
