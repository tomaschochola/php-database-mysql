<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql;

use NoDiscard;
use Override;

/**
 * @no-named-arguments
 */
readonly class MysqlSettings implements MysqlSettingsInterface
{
    #[Override()]
    public string $dbname;

    #[Override()]
    public string $host;

    #[Override()]
    public array $options;

    #[Override()]
    public string $password;

    #[Override()]
    public string $port;

    #[Override()]
    public string $socket;

    #[Override()]
    public string $username;

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

    #[NoDiscard()]
    #[Override()]
    public function clone(array $with): static
    {
        return clone ($this, $with);
    }
}
