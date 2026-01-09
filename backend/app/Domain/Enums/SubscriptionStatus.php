<?php

namespace App\Domain\Enums;

final class SubscriptionStatus
{
    public const ACTIVE = 'active';
    public const TRIALING = 'trialing';

    public static function isReadOnly(string $status): bool
    {
        return ! in_array($status, [self::ACTIVE, self::TRIALING], true);
    }
}

