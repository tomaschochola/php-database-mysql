<?php

/**
 * @author Tomáš Chochola <tomaschochola@tomaschochola.cz>
 * @copyright © 2026 Tomáš Chochola <tomaschochola@tomaschochola.cz>
 *
 * @license CC-BY-ND-4.0
 *
 * @see {@link https://creativecommons.org/licenses/by-nd/4.0/} License
 * @see {@link https://github.com/tomaschochola} GitHub Profile
 * @see {@link https://github.com/sponsors/tomaschochola} GitHub Sponsors
 */

declare(strict_types=1);

namespace TomasChochola\Database\Mysql;

use NoDiscard;
use Override;
use TomasChochola\Database\Mysql\Contract\LockInterface;
use TomasChochola\Database\Mysql\Contract\LockerInterface;
use TomasChochola\Database\Mysql\Contract\QueryInterface;
use UnexpectedValueException;

/**
 * @no-named-arguments
 */
readonly class MysqlLocker implements LockerInterface
{
    private QueryInterface $query;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    #[NoDiscard()]
    #[Override()]
    public function lock(string $name, int $wait = 3_600): LockInterface
    {
        $ok = $this->query->bool('SELECT GET_LOCK(?, ?)', [$name, $wait]);

        if (!$ok) {
            throw new UnexpectedValueException('GET_LOCK');
        }

        return new MysqlLock($this->query, $name);
    }

    #[NoDiscard()]
    #[Override()]
    public function try(string $name, int $wait = 0): LockInterface | null
    {
        $ok = $this->query->bool('SELECT GET_LOCK(?, ?)', [$name, $wait]);

        if ($ok) {
            return new MysqlLock($this->query, $name);
        }

        return null;
    }
}
