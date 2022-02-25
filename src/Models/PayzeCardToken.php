<?php

namespace PayzeIO\LaravelPayze\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;

/**
 * PayzeIO\LaravelPayze\Models\PayzeCardToken
 *
 * @property int $id
 * @property int|null $transaction_id
 * @property int|null $model_id
 * @property string|null $model_type
 * @property bool $active
 * @property string|null $card_mask
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $model
 * @property-read \PayzeIO\LaravelPayze\Models\PayzeTransaction|null $transaction
 * @method static Builder|PayzeCardToken active()
 * @method static Builder|PayzeCardToken newModelQuery()
 * @method static Builder|PayzeCardToken newQuery()
 * @method static Builder|PayzeCardToken query()
 * @method static Builder|PayzeCardToken whereActive($value)
 * @method static Builder|PayzeCardToken whereCardMask($value)
 * @method static Builder|PayzeCardToken whereCreatedAt($value)
 * @method static Builder|PayzeCardToken whereId($value)
 * @method static Builder|PayzeCardToken whereModelId($value)
 * @method static Builder|PayzeCardToken whereModelType($value)
 * @method static Builder|PayzeCardToken whereToken($value)
 * @method static Builder|PayzeCardToken whereTransactionId($value)
 * @method static Builder|PayzeCardToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PayzeCardToken extends Model
{
    /**
     * Guarded attributes.
     *
     * @var array
     */
     protected $guarded = [];

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
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
