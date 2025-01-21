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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Unique identifier for the payment
            $table->unsignedBigInteger('paid_by'); // The user who made the payment
            $table->unsignedBigInteger('paid_to'); // The user who received the payment
            $table->unsignedBigInteger('expense_id'); // The expense related to this payment (optional, if the payment is settling an expense)
            $table->decimal('amount', 10, 2); // The amount paid by the user
            $table->timestamps(); // Timestamp for when the payment was made
        
            // Foreign keys
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paid_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
