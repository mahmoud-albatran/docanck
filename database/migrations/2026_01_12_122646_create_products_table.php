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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('count')->nullable();
            $table->text('description');
            $table->float('Wholesale_price');
            $table->float('price');
            $table->text('image')->nullable();
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->enum('type', ['new', 'old'])->default('new');
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories', 'id')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->constrained('sub_categories', 'id')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
