<?php

namespace PayzeIO\LaravelPayze\Objects;

class Split
{
    /**
     * Amount to be received
     *
     * @var float
     */
    protected $amount;

    /**
     * IBAN of receiver
     *
     * @var string
     */
    protected $iban;

    /**
     * Delay (days) before receiving
     *
     * @var int
     */
    protected $payIn = 0;

    /**
     * Split constructor.
     *
     * @param float $amount
     * @param string $iban
     * @param int $payIn
     */
    public function __construct(float $amount, string $iban, int $payIn = 0)
    {
        $this->amount = $amount;
        $this->iban = $iban;
        $this->payIn = $payIn;
    }

    /**
     * @return array
     */
    public function toRequest(): array
    {
        return [
            'amount' => $this->amount,
            'iban' => $this->iban,
            'payIn' => $this->payIn,
        ];
    }
}
