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

use InvalidArgumentException;
use NoDiscard;
use UnexpectedValueException;

use function file_get_contents;
use function is_array;
use function is_string;
use function mb_trim;

/**
 * @no-named-arguments
 */
readonly class MysqlSettingsFactory
{
    /**
     * @param array<mixed, mixed> $options
     */
    #[NoDiscard]
    public function create(string $host, string $port, string $dbname, string $socket, string $username, string $password, array $options): MysqlSettings
    {
        return new MysqlSettings($host, $port, $dbname, $socket, $username, $password, $options);
    }

    /**
     * @param array<mixed, mixed> $settings
     */
    #[NoDiscard]
    public function createFrom(array $settings): MysqlSettings
    {
        if (!isset($settings['host']) || !is_string($settings['host'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['port']) || !is_string($settings['port'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['dbname']) || !is_string($settings['dbname'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['socket']) || !is_string($settings['socket'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['username']) || !is_string($settings['username'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['password']) || !is_string($settings['password'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['options']) || !is_array($settings['options'])) {
            throw new InvalidArgumentException('$settings');
        }

        $host = $settings['host'];
        $port = $settings['port'];
        $dbname = $settings['dbname'];
        $socket = $settings['socket'];
        $username = $settings['username'];
        $password = $settings['password'];
        $options = $settings['options'];

        $password = file_get_contents($password);

        if (!is_string($password)) {
            throw new UnexpectedValueException('file_get_contents');
        }

        $password = mb_trim($password);

        return $this->create($host, $port, $dbname, $socket, $username, $password, $options);
    }
}
