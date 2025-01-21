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
        Schema::create('expense_participants', function (Blueprint $table) {
            $table->id(); // Unique identifier for the record
            $table->unsignedBigInteger('expense_id'); // The expense this record is related to
            $table->unsignedBigInteger('user_id'); // The user who participated in the expense
            $table->decimal('amount', 10, 2); // The amount the user contributed to the expense
            $table->timestamps(); // Timestamp for when the record was created
        
            // Foreign keys
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_participants');
    }
};
