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

namespace TomasChochola\Pdo\Mysql;

use Override;
use TomasChochola\Pdo\LockInterface;
use TomasChochola\Pdo\QueryInterface;
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
