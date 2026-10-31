<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql;

use NoDiscard;

/**
 * @no-named-arguments
 */
interface MysqlSettingsInterface
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
    #[NoDiscard()]
    public function clone(array $with): static;
}
