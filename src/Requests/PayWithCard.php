<?php

namespace PayzeIO\LaravelPayze\Requests;

use PayzeIO\LaravelPayze\Concerns\PayRequestAttributes;
use PayzeIO\LaravelPayze\Enums\Method;
use PayzeIO\LaravelPayze\Exceptions\CardTokenInactiveException;
use PayzeIO\LaravelPayze\Facades\Payze;
use PayzeIO\LaravelPayze\Models\PayzeCardToken;
use PayzeIO\LaravelPayze\Models\PayzeTransaction;

class PayWithCard extends PayRequestAttributes
{
    /**
     * @var string
     */
    protected $method = Method::PAY_WITH_CARD;

    /**
     * @var \PayzeIO\LaravelPayze\Models\PayzeCardToken
     */
    protected $cardToken;

    /**
     * PayWithCard constructor.
     *
     * @param \PayzeIO\LaravelPayze\Models\PayzeCardToken $cardToken
     * @param float $amount
     *
     * @throws \PayzeIO\LaravelPayze\Exceptions\CardTokenInactiveException
     * @throws \Throwable
     */
    public function __construct(PayzeCardToken $cardToken, float $amount)
    {
        throw_unless($cardToken->active, new CardTokenInactiveException);

        $this->cardToken = $cardToken;
        $this->amount($amount);
    }

    /**
     * Process request via Payze facade
     *
     * @return \PayzeIO\LaravelPayze\Models\PayzeTransaction
     */
    public function process(): PayzeTransaction
    {
        return Payze::processTransaction($this, 'transactionInfo');
    }

    /**
     * @return array
     * @throws \PayzeIO\LaravelPayze\Exceptions\RoutesNotDefinedException
     * @throws \Throwable
     */
    public function toRequest(): array
    {
        return array_merge(parent::toRequest(), [
            'cardToken' => $this->cardToken->getToken(),
        ]);
    }
}
