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

namespace TomasChochola\Mysql;

use NoDiscard;

/**
 * @no-named-arguments
 */
interface PdoSettingsInterface
{
    public string $dbname { get; }

    public string $host { get; }

    /**
     * @var array<mixed, mixed>
     */
    public array $options { get; }

    public string $password { get; }

    public string $port { get; }

    public string $socket { get; }

    public string $username { get; }

    /**
     * @param array<mixed, mixed> $with
     */
    #[NoDiscard]
    public function clone(array $with): static;
}
