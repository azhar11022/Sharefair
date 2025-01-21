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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id(); // Unique identifier for the expense
            $table->unsignedBigInteger('user_paid'); // The user who paid the expense
            $table->unsignedBigInteger('group_id')->nullable(); // The group this expense is related to (nullable if not group-based)
            $table->decimal('amount', 10, 2); // The total amount of the expense
            $table->string('description')->nullable(); // Optional description for the expense (e.g., "Dinner with friends")
            $table->timestamps(); // Timestamp for when the expense was created
        
            // Foreign keys
            $table->foreign('user_paid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
