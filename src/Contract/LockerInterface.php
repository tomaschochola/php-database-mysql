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
