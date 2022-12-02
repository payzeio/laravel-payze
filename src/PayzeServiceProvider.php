<?php

namespace PayzeIO\LaravelPayze;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;
use PayzeIO\LaravelPayze\Observers\PayzeTransactionObserver;

class PayzeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_payze_transactions_table.php.stub' => $this->getMigrationFileName('create_payze_transactions_table.php', '_01'),
                __DIR__ . '/../database/migrations/create_payze_logs_table.php.stub' => $this->getMigrationFileName('create_payze_logs_table.php', '_02'),
                __DIR__ . '/../database/migrations/create_payze_card_tokens_table.php.stub' => $this->getMigrationFileName('create_payze_card_tokens_table.php', '_03'),
                __DIR__ . '/../database/migrations/add_transaction_id_to_payze_logs_table.php.stub' => $this->getMigrationFileName('add_transaction_id_to_payze_logs_table.php', '_04'),
                __DIR__ . '/../database/migrations/add_default_and_details_columns_to_payze_card_tokens_table.php.stub' => $this->getMigrationFileName('add_default_and_details_columns_to_payze_card_tokens_table.php', '_05'),
            ], 'payze-migrations');

            $this->publishes([
                __DIR__ . '/../config/payze.php' => config_path('payze.php'),
            ], 'payze-config');

            $this->publishes([
                __DIR__ . '/controllers/PayzeController.php.stub' => app_path('Http/Controllers/PayzeController.php'),
            ], 'payze-controllers');
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/payze.php', 'payze');

        $this->loadViewsFrom(__DIR__ . '/../views', 'payze');

        PayzeTransaction::observe(PayzeTransactionObserver::class);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $migrationFileName
     * @param string $prefix
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName, string $prefix = ''): string
    {
        $timestamp = date('Y_m_d_His') . $prefix;

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(fn($path) => File::glob($path . '*_' . $migrationFileName))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_$migrationFileName")
            ->first();
    }

    public function register()
    {
        $this->app->bind(Payze::class);
    }
}
