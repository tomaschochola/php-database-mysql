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
use Override;

/**
 * @no-named-arguments
 */
readonly class PdoSettings implements PdoSettingsInterface
{
    #[Override]
    public readonly string $dbname;

    #[Override]
    public readonly string $host;

    #[Override]
    public readonly array $options;

    #[Override]
    public readonly string $password;

    #[Override]
    public readonly string $port;

    #[Override]
    public readonly string $socket;

    #[Override]
    public readonly string $username;

    /**
     * @param array<mixed, mixed> $options
     */
    public function __construct(string $host, string $port, string $dbname, string $socket, string $username, string $password, array $options)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->socket = $socket;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    #[NoDiscard]
    #[Override]
    public function clone(array $with): static
    {
        return clone ($this, $with);
    }
}
