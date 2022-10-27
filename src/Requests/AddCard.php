<?php

namespace PayzeIO\LaravelPayze\Requests;

use Illuminate\Database\Eloquent\Model;
use PayzeIO\LaravelPayze\Concerns\PayRequest;
use PayzeIO\LaravelPayze\Enums\Method;

class AddCard extends PayRequest
{
    /**
     * @var string
     */
    protected string $method = Method::ADD_CARD;

    /**
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    protected ?Model $assignTo = null;

    /**
     * AddCard constructor.
     *
     * @param float $amount
     */
    public function __construct(float $amount = 0)
    {
        $this->amount($amount);
    }

    /**
     * Set the model for card token
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
     */
    public function assignTo(Model $model): self
    {
        $this->assignTo = $model;

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getAssignedModel(): ?Model
    {
        return $this->assignTo;
    }
}
