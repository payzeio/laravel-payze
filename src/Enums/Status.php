<?php

namespace PayzeIO\LaravelPayze\Enums;

class Status
{
    const PAID = [
        self::COMMITTED,
        self::BLOCKED,
        self::REFUNDED_PARTIALLY,
        self::CARD_ADDED,
        self::CARD_ADDED_FOR_SUBSCRIPTION,
    ];

    const INCOMPLETE = [
        self::CREATED,
        self::NOTHING,
        self::ERROR,
        self::REJECTED,
        self::TIMEOUT,
    ];

    const BLOCKED = 'Blocked';

    const CARD_ADDED = 'CardAdded';

    const CARD_ADDED_FOR_SUBSCRIPTION = 'CardAddedForSubscription';

    const COMMIT_FAILED = 'CommitFailed';

    const COMMITTED = 'Committed';

    const CREATED = 'Created';

    const NOTHING = 'Nothing';

    const ERROR = 'Error';

    const REFUNDED = 'Refunded';

    const REFUNDED_PARTIALLY = 'RefundedPartially';

    const REJECTED = 'Rejected';

    const TIMEOUT = 'Timeout';

    /**
     * Check if status is paid
     *
     * @param string $status
     *
     * @return bool
     */
    public static function isPaid(string $status): bool
    {
        return in_array($status, self::PAID);
    }

    /**
     * Check if status is completed
     *
     * @param string $status
     *
     * @return bool
     */
    public static function isCompleted(string $status): bool
    {
        return !in_array($status, self::INCOMPLETE);
    }
}
