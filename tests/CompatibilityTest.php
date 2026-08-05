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

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Stringable;
use TomasChochola\Pdo\LockInterface;
use TomasChochola\Pdo\LockerInterface;
use TomasChochola\Pdo\Mysql\MysqlLock;
use TomasChochola\Pdo\Mysql\MysqlLocker;
use TomasChochola\Pdo\Mysql\MysqlQuery;
use TomasChochola\Pdo\Mysql\MysqlSettings;
use TomasChochola\Pdo\Mysql\MysqlSettingsFactory;
use TomasChochola\Pdo\QueryInterface;
use UnexpectedValueException;

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
#[CoversClass(MysqlLock::class)]
#[CoversClass(MysqlLocker::class)]
#[Small()]
final class CompatibilityTest extends TestCase
{
    #[Test()]
    public function invalidSettingsAreRejected(): void
    {
        $factory = new MysqlSettingsFactory();

        $this->expectException(InvalidArgumentException::class);

        self::fail('Invalid settings unexpectedly accepted: ' . $factory->createFrom([])::class);
    }

    #[Test()]
    public function lockReportsFailedRelease(): void
    {
        $query = self::createStub(QueryInterface::class);
        $query->method('bool')->willReturn(false);
        $lock = new MysqlLock($query, 'job');

        $this->expectException(UnexpectedValueException::class);

        $lock->unlock();
    }

    #[Test()]
    public function lockerReportsFailedRequiredLock(): void
    {
        $query = self::createStub(QueryInterface::class);
        $query->method('bool')->willReturn(false);
        $locker = new MysqlLocker($query);

        self::assertNull($locker->try('optional'));
        $this->expectException(UnexpectedValueException::class);

        self::fail('Required lock unexpectedly acquired: ' . $locker->lock('required')::class);
    }

    #[Test()]
    public function lockerUsesMysqlAdvisoryLockOperations(): void
    {
        $calls = [];
        $query = self::createStub(QueryInterface::class);

        $query->method('bool')->willReturnCallback(static function (Stringable | string $sql, array $params) use (&$calls): bool {
            $calls[] = [(string) $sql, $params];

            return true;
        });

        $locker = new MysqlLocker($query);
        $lock = $locker->lock('job', 7);

        $lock->unlock();

        self::assertInstanceOf(MysqlLock::class, $locker->try('optional'));

        self::assertSame([
            ['SELECT GET_LOCK(?, ?)', ['job', 7]],
            ['SELECT RELEASE_LOCK(?)', ['job']],
            ['SELECT GET_LOCK(?, ?)', ['optional', 0]],
        ], $calls);
    }

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
            self::assertSame('localhost', $settings->host);
            self::assertSame('3306', $settings->port);
            self::assertSame('database', $settings->dbname);
            self::assertSame('', $settings->socket);
            self::assertSame('username', $settings->username);
            self::assertSame([], $settings->options);

            $changed = $settings->clone(['dbname' => 'other']);

            self::assertNotSame($settings, $changed);
            self::assertSame('database', $settings->dbname);
            self::assertSame('other', $changed->dbname);
        } finally {
            self::assertTrue(unlink($passwordFile));
        }
    }
}
