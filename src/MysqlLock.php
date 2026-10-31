<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql;

use Override;
use TomasChochola\Database\Mysql\Contract\LockInterface;
use TomasChochola\Database\Mysql\Contract\QueryInterface;
use UnexpectedValueException;

/**
 * @no-named-arguments
 */
readonly class MysqlLock implements LockInterface
{
    private string $name;

    private QueryInterface $query;

    public function __construct(QueryInterface $query, string $name)
    {
        $this->query = $query;
        $this->name = $name;
    }

    #[Override()]
    public function unlock(): void
    {
        $ok = $this->query->bool('SELECT RELEASE_LOCK(?)', [$this->name]);

        if (!$ok) {
            throw new UnexpectedValueException('RELEASE_LOCK');
        }
    }
}
