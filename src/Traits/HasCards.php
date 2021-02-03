<?php

namespace PayzeIO\LaravelPayze\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use PayzeIO\LaravelPayze\Models\PayzeCardToken;

trait HasCards
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function cards(): MorphMany
    {
        return $this->morphMany(PayzeCardToken::class, 'model');
    }
}
