<?php

namespace PayzeIO\LaravelPayze\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

trait HasTransactions
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(PayzeTransaction::class, 'model');
    }
}
