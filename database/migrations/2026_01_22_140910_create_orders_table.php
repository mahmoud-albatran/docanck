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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('Price_before_discount', 20, 2)->nullable();
            $table->decimal('total_price', 20, 2);
            $table->enum('status', ['pending', 'shipped', 'complete', 'cancel'])->default('pending');
            $table->enum('payment', ['BankOfPalestine', 'PalPay', 'Jawwal', 'OnReceipt'])->default('OnReceipt');
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('user_adresses', 'id')->nullOnDelete();
            $table->foreignId('code_id')->nullable()->constrained('discount_codes', 'id')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
