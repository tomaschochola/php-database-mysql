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

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use TomasChochola\Pdo\LockInterface;
use TomasChochola\Pdo\LockerInterface;
use TomasChochola\Pdo\Mysql\MysqlLock;
use TomasChochola\Pdo\Mysql\MysqlLocker;
use TomasChochola\Pdo\Mysql\MysqlQuery;
use TomasChochola\Pdo\Mysql\MysqlSettings;
use TomasChochola\Pdo\Mysql\MysqlSettingsFactory;
use TomasChochola\Pdo\QueryInterface;

use function file_put_contents;
use function is_int;
use function is_string;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @internal
 *
 * @no-named-arguments
 */
#[CoversClass(MysqlSettingsFactory::class)]
#[CoversClass(MysqlSettings::class)]
#[Small()]
class CompatibilityTest extends TestCase
{
    #[Test()]
    public function publicContractsRemainImplemented(): void
    {
        self::assertContains(QueryInterface::class, (new ReflectionClass(MysqlQuery::class))->getInterfaceNames());
        self::assertContains(LockerInterface::class, (new ReflectionClass(MysqlLocker::class))->getInterfaceNames());
        self::assertContains(LockInterface::class, (new ReflectionClass(MysqlLock::class))->getInterfaceNames());
    }

    #[Test()]
    public function settingsFactoryReadsPasswordFromFile(): void
    {
        $passwordFile = tempnam(sys_get_temp_dir(), 'mysql-password-');

        if (!is_string($passwordFile)) {
            self::fail('Unable to create a temporary password file.');
        }

        try {
            $written = file_put_contents($passwordFile, " secret\n");

            if (!is_int($written)) {
                self::fail('Unable to write a temporary password file.');
            }

            $settings = (new MysqlSettingsFactory())->createFrom([
                'host' => 'localhost',
                'port' => '3306',
                'dbname' => 'database',
                'socket' => '',
                'username' => 'username',
                'password' => $passwordFile,
                'options' => [],
            ]);

            self::assertSame('secret', $settings->password);
        } finally {
            unlink($passwordFile);
        }
    }
}
