<?php

namespace PayzeIO\LaravelPayze\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PayzeIO\LaravelPayze\Models\PayzeLog
 *
 * @property int $id
 * @property string|null $message
 * @property array|null $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayzeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PayzeLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'payload',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * PayzeLog constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('payze.logs_table', 'payze_logs');
    }
}
