<?php

namespace PayzeIO\LaravelPayze\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use PayzeIO\LaravelPayze\Enums\Status;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;
use PayzeIO\LaravelPayze\Requests\GetTransactionInfo;

class UpdateIncompleteTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payze:update-incomplete-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update incomplete transactions after 20 minutes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Abandoned transactions are rejected automatically after 10-15 minutes
        $transactions = PayzeTransaction::incomplete()->where('created_at', '<=', now()->subMinutes(20))->get();

        if ($transactions->isEmpty()) {
            $this->info('All transactions are completed');

            return;
        }

        $this->info(sprintf('Updating %s transactions...', $transactions->count()));

        $transactions->each(function (PayzeTransaction $transaction) {
            try {
                $result = GetTransactionInfo::request($transaction)->process();
            } catch (Exception $e) {
                $this->error(sprintf('Can\'t update transaction (#%s) %s, setting status to Error.', $transaction->id, $transaction->transaction_id));

                $transaction->update([
                    'status' => Status::ERROR,
                    'is_completed' => true,
                ]);

                return;
            }

            $this->info(sprintf('%s - %s', $transaction->transaction_id, $result->status));
        });

        $this->info(sprintf('%s transactions updated successfully', $transactions->count()));
    }
}
