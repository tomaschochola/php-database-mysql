<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql\Contract;

/**
 * @no-named-arguments
 */
interface LockInterface
{
    public function unlock(): void;
}
