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

namespace TomasChochola\Database\Mysql;

use NoDiscard;
use Override;
use PDO;
use PDOStatement;
use Pdo\Mysql;
use Stringable;
use TomasChochola\Database\Mysql\Contract\QueryInterface;
use UnexpectedValueException;
use stdClass;

use function assert;
use function is_bool;
use function is_int;
use function is_string;

/**
 * @no-named-arguments
 */
readonly class MysqlQuery implements QueryInterface
{
    private Mysql $mysql;

    public function __construct(Mysql $mysql)
    {
        $this->mysql = $mysql;
    }

    #[Override()]
    public function begin(): void
    {
        $ok = $this->mysql->beginTransaction();

        if ($ok !== true) {
            throw new UnexpectedValueException('beginTransaction');
        }
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    #[Override()]
    public function bool(Stringable | string $sql, array $params = []): bool
    {
        $stm = $this->mysql->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $ok = $stm->bindColumn(1, $bool, PDO::PARAM_BOOL);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindColumn');
        }

        $ok = $stm->fetch(PDO::FETCH_BOUND);

        if ($ok !== true) {
            throw new UnexpectedValueException('fetch');
        }

        if (!is_bool($bool)) {
            throw new UnexpectedValueException('$bool');
        }

        return $bool;
    }

    #[Override()]
    public function commit(): void
    {
        $ok = $this->mysql->commit();

        if ($ok !== true) {
            throw new UnexpectedValueException('commit');
        }
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    #[Override()]
    public function execute(Stringable | string $sql, array $params = []): int
    {
        $stm = $this->mysql->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        return $stm->rowCount();
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    #[Override()]
    public function insert(string $sql, array $params = []): string
    {
        $count = $this->execute($sql, $params);

        if ($count !== 1) {
            throw new UnexpectedValueException('rowCount');
        }

        $id = $this->mysql->lastInsertId();

        if (!is_string($id)) {
            throw new UnexpectedValueException('lastInsertId');
        }

        return $id;
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    #[Override()]
    public function int(Stringable | string $sql, array $params = []): int
    {
        $stm = $this->mysql->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $ok = $stm->bindColumn(1, $int, PDO::PARAM_INT);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindColumn');
        }

        $ok = $stm->fetch(PDO::FETCH_BOUND);

        if ($ok !== true) {
            throw new UnexpectedValueException('fetch');
        }

        if (is_int($int)) {
            return $int;
        }

        throw new UnexpectedValueException('$int');
    }

    /**
     * @template TObject of object
     *
     * @param array<mixed, mixed> $params
     * @param class-string<TObject> $class
     *
     * @return TObject|null
     */
    #[NoDiscard()]
    #[Override()]
    public function object(string $sql, array $params = [], string $class = stdClass::class): object | null
    {
        $stm = $this->mysql->prepare($sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        return self::single($stm, $class);
    }

    /**
     * @template TObject of object
     *
     * @param array<mixed, mixed> $params
     * @param class-string<TObject> $class
     *
     * @return iterable<mixed, TObject>
     */
    #[NoDiscard()]
    #[Override()]
    public function objects(string $sql, array $params = [], string $class = stdClass::class): iterable
    {
        $stm = $this->mysql->prepare($sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        yield from self::many($stm, $class);
    }

    #[Override()]
    public function rollback(): void
    {
        $ok = $this->mysql->rollBack();

        if ($ok !== true) {
            throw new UnexpectedValueException('rollBack');
        }
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[Override()]
    public function run(Stringable | string $sql, array $params = []): void
    {
        $stm = $this->mysql->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }
    }

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    #[Override()]
    public function string(Stringable | string $sql, array $params = []): string
    {
        $stm = $this->mysql->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $ok = $stm->bindColumn(1, $string, PDO::PARAM_STR);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindColumn');
        }

        $ok = $stm->fetch(PDO::FETCH_BOUND);

        if ($ok !== true) {
            throw new UnexpectedValueException('fetch');
        }

        if (is_string($string)) {
            return $string;
        }

        throw new UnexpectedValueException('$string');
    }

    /**
     * @template TObject of object
     *
     * @param class-string<TObject> $class
     *
     * @return iterable<mixed, TObject>
     */
    #[NoDiscard()]
    private static function many(PDOStatement $stm, string $class): iterable
    {
        while (true) {
            $object = self::single($stm, $class);

            if ($object === null) {
                return;
            }

            yield $object;
        }
    }

    /**
     * @template TObject of object
     *
     * @param class-string<TObject> $class
     *
     * @return TObject|null
     */
    #[NoDiscard()]
    private static function single(PDOStatement $stm, string $class): object | null
    {
        $object = $stm->fetchObject($class);

        if ($object === false) {
            return null;
        }

        assert($object instanceof $class);

        return $object;
    }
}
