<?php

namespace PayzeIO\LaravelPayze\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class PayzeCardToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'active',
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * PayzeCardToken constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('payze.card_tokens_table', 'payze_card_tokens');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PayzeTransaction::class, 'transaction_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Encrypt a card token before saving to DB
     *
     * @param string $value
     */
    public function setTokenAttribute(string $value): void
    {
        if (empty($value)) {
            return;
        }

        $this->attributes['token'] = Crypt::encryptString($value);
    }

    /**
     * Return a decrypted token
     *
     * @return string|null
     */
    public function getToken(): string
    {
        return Crypt::decryptString($this->token);
    }
}
