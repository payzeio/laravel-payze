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
        Schema::create(config('payze.transactions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->string('model_type')->nullable();
            $table->string('method')->nullable();
            $table->string('status');
            $table->boolean('is_paid')->default(0);
            $table->boolean('is_completed')->default(0);
            $table->string('transaction_id')->unique()->index();
            $table->decimal('amount', 10);
            $table->decimal('final_amount', 10)->nullable();
            $table->decimal('refunded', 10)->nullable();
            $table->decimal('commission')->nullable();
            $table->boolean('refundable')->default(0);
            $table->string('currency');
            $table->string('lang');
            $table->json('split')->nullable();
            $table->boolean('can_be_committed')->default(0);
            $table->string('result_code')->nullable();
            $table->string('card_mask')->nullable();
            $table->json('log')->nullable();
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
        Schema::dropIfExists(config('payze.transactions_table'));
    }
};
