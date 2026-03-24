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
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;
use function is_string;

/**
 * @no-named-arguments
 */
readonly class PdoSettingsFactory
{
    public function __construct() {}

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        return new self();
    }

    #[NoDiscard]
    public static function produce(ContainerInterface $container): PdoSettings
    {
        $factory = $container->get(self::class);

        assert($factory instanceof self);

        return $factory->create([
            'host' => $container->get('PDO_HOST'),
            'port' => $container->get('PDO_PORT'),
            'dbname' => $container->get('PDO_DBNAME'),
            'socket' => $container->get('PDO_SOCKET'),
            'username' => $container->get('PDO_USERNAME'),
            'password' => $container->get('PDO_PASSWORD'),
            'options' => $container->get('PDO_OPTIONS'),
        ]);
    }

    /**
     * @param array{host: mixed, port: mixed, dbname: mixed, socket: mixed, username: mixed, password: mixed, options: mixed} $settings
     */
    #[NoDiscard]
    public function create(array $settings): PdoSettings
    {
        assert(is_string($settings['host']));
        assert(is_string($settings['port']));
        assert(is_string($settings['dbname']));
        assert(is_string($settings['socket']));
        assert(is_string($settings['username']));
        assert(is_string($settings['password']));
        assert(is_array($settings['options']));

        $host = $settings['host'];
        $port = $settings['port'];
        $dbname = $settings['dbname'];
        $socket = $settings['socket'];
        $username = $settings['username'];
        $password = $settings['password'];
        $options = $settings['options'];

        return new PdoSettings($host, $port, $dbname, $socket, $username, $password, $options);
    }
}
