<?php

namespace PayzeIO\LaravelPayze\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * PayzeIO\LaravelPayze\Models\PayzeTransaction
 *
 * @property int $id
 * @property int|null $model_id
 * @property string|null $model_type
 * @property string|null $method
 * @property string $status
 * @property int $is_paid
 * @property int $is_completed
 * @property string $transaction_id
 * @property float $amount
 * @property float|null $final_amount
 * @property bool|null $refunded
 * @property float|null $commission
 * @property bool $refundable
 * @property string $currency
 * @property string $lang
 * @property array|null $split
 * @property bool $can_be_committed
 * @property string|null $result_code
 * @property string|null $card_mask
 * @property array|null $log
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\PayzeIO\LaravelPayze\Models\PayzeCardToken[] $cards
 * @property-read int|null $cards_count
 * @property-read Model|\Eloquent $model
 * @method static Builder|PayzeTransaction completed()
 * @method static Builder|PayzeTransaction incomplete()
 * @method static Builder|PayzeTransaction newModelQuery()
 * @method static Builder|PayzeTransaction newQuery()
 * @method static Builder|PayzeTransaction nonRefundable()
 * @method static Builder|PayzeTransaction paid()
 * @method static Builder|PayzeTransaction query()
 * @method static Builder|PayzeTransaction refundable()
 * @method static Builder|PayzeTransaction unpaid()
 * @method static Builder|PayzeTransaction whereAmount($value)
 * @method static Builder|PayzeTransaction whereCanBeCommitted($value)
 * @method static Builder|PayzeTransaction whereCardMask($value)
 * @method static Builder|PayzeTransaction whereCommission($value)
 * @method static Builder|PayzeTransaction whereCreatedAt($value)
 * @method static Builder|PayzeTransaction whereCurrency($value)
 * @method static Builder|PayzeTransaction whereFinalAmount($value)
 * @method static Builder|PayzeTransaction whereId($value)
 * @method static Builder|PayzeTransaction whereIsCompleted($value)
 * @method static Builder|PayzeTransaction whereIsPaid($value)
 * @method static Builder|PayzeTransaction whereLang($value)
 * @method static Builder|PayzeTransaction whereLog($value)
 * @method static Builder|PayzeTransaction whereMethod($value)
 * @method static Builder|PayzeTransaction whereModelId($value)
 * @method static Builder|PayzeTransaction whereModelType($value)
 * @method static Builder|PayzeTransaction whereRefundable($value)
 * @method static Builder|PayzeTransaction whereRefunded($value)
 * @method static Builder|PayzeTransaction whereResultCode($value)
 * @method static Builder|PayzeTransaction whereSplit($value)
 * @method static Builder|PayzeTransaction whereStatus($value)
 * @method static Builder|PayzeTransaction whereTransactionId($value)
 * @method static Builder|PayzeTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PayzeTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'method',
        'final_amount',
        'refunded',
        'status',
        'is_paid',
        'is_completed',
        'transaction_id',
        'amount',
        'currency',
        'lang',
        'split',
        'commission',
        'can_be_committed',
        'refundable',
        'result_code',
        'card_mask',
        'log',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'final_amount' => 'float',
        'commission' => 'float',
        'split' => 'array',
        'can_be_committed' => 'boolean',
        'refunded' => 'boolean',
        'refundable' => 'boolean',
        'log' => 'array',
    ];

    /**
     * PayzeTranscation constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('payze.transactions_table', 'payze_transactions');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards(): HasMany
    {
        return $this->hasMany(PayzeCardToken::class, 'transaction_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_paid', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('is_paid', false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefundable(Builder $query): Builder
    {
        return $query->where('refundable', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonRefundable(Builder $query): Builder
    {
        return $query->where('refundable', false);
    }
}
