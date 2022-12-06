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
        Schema::create(config('payze.card_tokens_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->foreign('transaction_id')
                ->references('id')
                ->on(config('payze.transactions_table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->string('model_type')->nullable();
            $table->boolean('active')->default(0);
            $table->string('card_mask')->nullable();
            $table->text('token');
            $table->timestamps();

            $table->index(['model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('payze.card_tokens_table'));
    }
};
