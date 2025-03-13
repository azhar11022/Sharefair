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
        Schema::table('expenses', function (Blueprint $table) {
            // Add the receipt_id column
            $table->unsignedBigInteger('recipt_id')->nullable()->after('id'); // Add the foreign key column

            // Define the foreign key constraint
            $table->foreign('recipt_id')
                  ->references('id')
                  ->on('recipts')
                  ->onDelete('cascade'); // Deletes expense if the related receipt is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['recipt_id']);

            // Then drop the column
            $table->dropColumn('recipt_id');
        });
    }
};
