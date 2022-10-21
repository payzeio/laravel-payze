<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('payze.logs_table'), function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->after('id')->constrained(config('payze.transactions_table'))->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('payze.logs_table'), function (Blueprint $table) {
            $table->dropConstrainedForeignId('transaction_id');
        });
    }
};
