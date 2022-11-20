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
        Schema::table(config('payze.card_tokens_table'), function (Blueprint $table) {
            $table->boolean('default')->default(false)->after('active');
            $table->string('cardholder')->nullable()->after('card_mask');
            $table->string('brand')->nullable()->after('cardholder');
            $table->date('expiration_date')->nullable()->after('brand');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('payze.card_tokens_table'), function (Blueprint $table) {
            $table->dropColumn([
                'default',
                'cardholder',
                'brand',
                'expiration_date',
            ]);
        });
    }
};
