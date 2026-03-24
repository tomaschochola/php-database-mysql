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
use Pdo\Mysql;
use Psr\Container\ContainerInterface;

use function array_replace;
use function assert;
use function implode;

/**
 * @no-named-arguments
 */
readonly class MysqlFactory
{
    public function __construct() {}

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        return new self();
    }

    #[NoDiscard]
    public static function produce(ContainerInterface $container): Mysql
    {
        $factory = $container->get(self::class);
        $settings = $container->get(PdoSettingsInterface::class);

        assert($factory instanceof self);
        assert($settings instanceof PdoSettingsInterface);

        return $factory->create($settings);
    }

    #[NoDiscard]
    public function create(PdoSettingsInterface $settings): Mysql
    {
        $dsn = [];

        if ($settings->host !== '') {
            $dsn[] = 'host=' . $settings->host;
        }

        if ($settings->port !== '') {
            $dsn[] = 'port=' . $settings->port;
        }

        if ($settings->dbname !== '') {
            $dsn[] = 'dbname=' . $settings->dbname;
        }

        if ($settings->socket !== '') {
            $dsn[] = 'unix_socket=' . $settings->socket;
        }

        $dsn[] = 'charset=utf8mb4';

        return new Mysql('mysql:' . implode(';', $dsn), $settings->username, $settings->password, array_replace([
            Mysql::ATTR_DEFAULT_FETCH_MODE => Mysql::FETCH_OBJ,
            Mysql::ATTR_EMULATE_PREPARES => false,
            Mysql::ATTR_ERRMODE => Mysql::ERRMODE_EXCEPTION,
            Mysql::ATTR_MULTI_STATEMENTS => false,
            Mysql::ATTR_INIT_COMMAND => 'SET SESSION time_zone = \'+00:00\'',
        ], $settings->options));
    }
}
