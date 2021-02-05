<?php

namespace PayzeIO\LaravelPayze;

use Illuminate\Support\ServiceProvider;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;
use PayzeIO\LaravelPayze\Observers\PayzeTransactionObserver;

class PayzeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_payze_transactions_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time() - 10) . '_create_payze_transactions_table.php'),
            __DIR__ . '/../database/migrations/create_payze_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time() - 5) . '_create_payze_logs_table.php'),
            __DIR__ . '/../database/migrations/create_payze_card_tokens_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_payze_card_tokens_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/payze.php' => config_path('payze.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/payze.php', 'payze');

        $this->loadViewsFrom(__DIR__ . '/../views', 'payze');

        PayzeTransaction::observe(PayzeTransactionObserver::class);
    }

    public function register()
    {
        $this->app->bind(Payze::class);
    }
}
