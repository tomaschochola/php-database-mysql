<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql\Contract;

use NoDiscard;

/**
 * @no-named-arguments
 */
interface LockerInterface
{
    #[NoDiscard()]
    public function lock(string $name, int $wait = 3_600): LockInterface;

    #[NoDiscard()]
    public function try(string $name, int $wait = 0): LockInterface | null;
}
