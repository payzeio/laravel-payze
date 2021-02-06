<?php

namespace PayzeIO\LaravelPayze\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use PayzeIO\LaravelPayze\Enums\Currency;
use PayzeIO\LaravelPayze\Enums\Language;
use PayzeIO\LaravelPayze\Exceptions\PaymentRequestException;
use PayzeIO\LaravelPayze\Exceptions\RoutesNotDefinedException;
use PayzeIO\LaravelPayze\Exceptions\UnsupportedCurrencyException;
use PayzeIO\LaravelPayze\Exceptions\UnsupportedLanguageException;
use PayzeIO\LaravelPayze\Objects\Split;

abstract class PayRequestAttributes extends ApiRequest
{
    /**
     * @var float
     */
    protected $amount = 0;

    /**
     * @var array
     */
    protected $split = [];

    /**
     * @var string
     */
    protected $currency = Currency::DEFAULT;

    /**
     * @var string
     */
    protected $lang = Language::DEFAULT;

    /**
     * @var bool
     */
    protected $preauthorize = false;

    /**
     * @var bool
     */
    protected $raw = false;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function amount(float $amount): self
    {
        $this->amount = max($amount, 0);

        return $this;
    }

    /**
     * Split transaction to different IBANs
     *
     * @param mixed $splits
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\PaymentRequestException
     * @throws \Throwable
     */
    public function split($splits): self
    {
        $splits = !is_array($splits) ? func_get_args() : $splits;

        foreach ($splits as $split) {
            throw_unless(is_a($split, Split::class), new PaymentRequestException('Incorrect format. Please pass Split object'));
        }

        $this->split = $splits;

        return $this;
    }

    /**
     * Switch payment page language
     *
     * @param string $lang
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\UnsupportedLanguageException|\Throwable
     */
    public function language(string $lang): self
    {
        throw_unless(Language::check($lang), new UnsupportedLanguageException($lang));

        $this->lang = $lang;

        return $this;
    }

    /**
     * Switch payment page language
     *
     * @param string $currency
     *
     * @return $this
     * @throws \PayzeIO\LaravelPayze\Exceptions\UnsupportedCurrencyException|\Throwable
     */
    public function currency(string $currency): self
    {
        $currency = strtoupper($currency);

        throw_unless(Currency::check($currency), new UnsupportedCurrencyException($currency));

        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the model for transaction
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
     */
    public function for(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set preauthorize option for transaction
     *
     * @param bool $preauthorize
     *
     * @return $this
     */
    public function preauthorize(bool $preauthorize = true): self
    {
        $this->preauthorize = $preauthorize;

        return $this;
    }

    /**
     * Set raw option for transaction
     *
     * @param bool $raw
     *
     * @return $this
     */
    public function raw(bool $raw = true): self
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * @return bool
     */
    public function getPreauthorize(): bool
    {
        return $this->preauthorize;
    }

    /**
     * @return bool
     */
    public function getRaw(): bool
    {
        return $this->raw;
    }

    /**
     * @return array
     */
    public function getSplit(): array
    {
        return $this->split;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @return array
     * @throws \PayzeIO\LaravelPayze\Exceptions\RoutesNotDefinedException
     * @throws \Throwable
     */
    public function toRequest(): array
    {
        $successName = config('payze.routes.success');
        $failName = config('payze.routes.fail');

        throw_unless(Route::has($successName) && Route::has($failName), new RoutesNotDefinedException);

        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
            'preauthorize' => $this->preauthorize,
            'split' => array_map(function (Split $split) {
                return $split->toRequest();
            }, $this->split),
            'callback' => route($successName),
            'callbackError' => route($failName),
        ];
    }

    /**
     * @return array
     */
    public function toModel(): array
    {
        return [
            'model_id' => optional($this->model)->id,
            'model_type' => $this->model ? get_class($this->model) : null,
            'method' => $this->method,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
        ];
    }
}
