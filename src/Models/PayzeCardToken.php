<?php

namespace PayzeIO\LaravelPayze\Models;

use Carbon\Carbon;
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
 * @property bool $default
 * @property string|null $card_mask
 * @property string|null $cardholder
 * @property string|null $brand
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $model
 * @property-read \PayzeIO\LaravelPayze\Models\PayzeTransaction|null $transaction
 * @method static Builder|PayzeCardToken active()
 * @method static Builder|PayzeCardToken default()
 * @method static Builder|PayzeCardToken expired()
 * @method static Builder|PayzeCardToken inactive()
 * @method static Builder|PayzeCardToken newModelQuery()
 * @method static Builder|PayzeCardToken newQuery()
 * @method static Builder|PayzeCardToken query()
 * @method static Builder|PayzeCardToken whereActive($value)
 * @method static Builder|PayzeCardToken whereBrand($value)
 * @method static Builder|PayzeCardToken whereCardMask($value)
 * @method static Builder|PayzeCardToken whereCardholder($value)
 * @method static Builder|PayzeCardToken whereCreatedAt($value)
 * @method static Builder|PayzeCardToken whereDefault($value)
 * @method static Builder|PayzeCardToken whereExpirationDate($value)
 * @method static Builder|PayzeCardToken whereId($value)
 * @method static Builder|PayzeCardToken whereModelId($value)
 * @method static Builder|PayzeCardToken whereModelType($value)
 * @method static Builder|PayzeCardToken whereToken($value)
 * @method static Builder|PayzeCardToken whereTransactionId($value)
 * @method static Builder|PayzeCardToken whereUpdatedAt($value)
 * @method static Builder|PayzeCardToken withInactive()
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
        'default' => 'boolean',
        'expiration_date' => 'datetime',
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

    protected static function booted()
    {
        /*
         * Add global active scope
         */
        static::addGlobalScope('active', fn(Builder $builder) => $builder->where('active', true));
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
    public function scopeWithInactive(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('default', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expiration_date', '>=', Carbon::now());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->withInactive()->where('active', false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiration_date', '<', Carbon::now());
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

    /**
     * Mark current card as default
     * Unmark all other cards of the same model
     *
     * @return $this
     */
    public function markAsDefault(): self
    {
        self::where('model_type', $this->model_type)
            ->where('model_id', $this->model_id)
            ->where('id', '!=', $this->id)
            ->where('default', true)
            ->update(['default' => false]);

        return tap($this)->update([
            'default' => true,
        ]);
    }

    /**
     * Check if current card is not expired
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (empty($this->expiration_date)) {
            return true;
        }

        return $this->expiration_date->isFuture();
    }

    /**
     * Check if current card is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (empty($this->expiration_date)) {
            return false;
        }

        return $this->expiration_date->isPast();
    }
}
