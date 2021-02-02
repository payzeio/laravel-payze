<?php

namespace PayzeIO\LaravelPayze\Enums;

class Method
{
    const JUST_PAY = 'justPay';

    const ADD_CARD = 'addCard';

    const COMMIT = 'commit';

    const PAY_WITH_CARD = 'payWithCard';

    const REFUND = 'refund';

    const TRANSACTION_INFO = 'getTransactionInfo';

    const BALANCE = 'getBalance';
}
