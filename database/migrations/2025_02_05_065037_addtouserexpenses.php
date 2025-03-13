<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');

        });
    }
    
    public function down()
    {
        Schema::table('user_expenses', function (Blueprint $table) {
            $table->dropColumn('expense_id');  // Drop 'expense_in' column if we rollback
        });
    }
};
