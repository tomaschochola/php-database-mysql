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
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;
use Stringable;
use UnexpectedValueException;
use stdClass;

use function array_key_exists;
use function assert;
use function filter_var;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;

use const FILTER_DEFAULT;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;
use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

/**
 * @no-named-arguments
 */
readonly class PdoQuery
{
    public readonly PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $pdo = $container->get(PDO::class);

        assert($pdo instanceof PDO);

        return new self($pdo);
    }

    public function begin(): void
    {
        $ok = $this->pdo->beginTransaction();

        if ($ok !== true) {
            throw new UnexpectedValueException('beginTransaction');
        }
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function bool(Stringable|string $sql, array $params = []): bool
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $row = $stm->fetch(PDO::FETCH_NUM);

        if (!is_array($row)) {
            throw new UnexpectedValueException('fetch');
        }

        if (!array_key_exists(0, $row)) {
            throw new UnexpectedValueException('$row');
        }

        $flag = $row[0];

        $bool = match ($flag) {
            false => false,
            true => true,
            0 => false,
            1 => true,
            '0' => false,
            '1' => true,
            'f' => false,
            'F' => false,
            'false' => false,
            'FALSE' => false,
            't' => true,
            'T' => true,
            'true' => true,
            'TRUE' => true,
            default => filter_var($flag, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        };

        if (!is_bool($bool)) {
            throw new UnexpectedValueException('$flag');
        }

        return $bool;
    }

    public function commit(): void
    {
        $ok = $this->pdo->commit();

        if ($ok !== true) {
            throw new UnexpectedValueException('commit');
        }
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function execute(Stringable|string $sql, array $params = []): int
    {
        $stm = $this->pdo->prepare((string) $sql);

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
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function float(Stringable|string $sql, array $params = []): float
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $row = $stm->fetch(PDO::FETCH_NUM);

        if (!is_array($row)) {
            throw new UnexpectedValueException('fetch');
        }

        if (!array_key_exists(0, $row)) {
            throw new UnexpectedValueException('$row');
        }

        $float = $row[0];

        if (is_int($float) || is_float($float)) {
            return (float) $float;
        }

        $float = filter_var($float, FILTER_VALIDATE_FLOAT);

        if (!is_float($float)) {
            throw new UnexpectedValueException('$float');
        }

        return $float;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function insert(string $sql, array $params = []): string
    {
        $count = $this->execute($sql, $params);

        if ($count !== 1) {
            throw new UnexpectedValueException('rowCount');
        }

        $id = $this->pdo->lastInsertId();

        if (!is_string($id)) {
            throw new UnexpectedValueException('lastInsertId');
        }

        return $id;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function int(Stringable|string $sql, array $params = []): int
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $row = $stm->fetch(PDO::FETCH_NUM);

        if (!is_array($row)) {
            throw new UnexpectedValueException('fetch');
        }

        if (!array_key_exists(0, $row)) {
            throw new UnexpectedValueException('$row');
        }

        $int = $row[0];

        if (is_int($int)) {
            return $int;
        }

        $int = filter_var($int, FILTER_VALIDATE_INT);

        if (!is_int($int)) {
            throw new UnexpectedValueException('$int');
        }

        return $int;
    }

    /**
     * @template TObject of object
     * @param array<int|string, mixed> $params
     * @param class-string<TObject> $class
     * @return TObject|null
     */
    #[NoDiscard]
    public function object(string $sql, array $params = [], string $class = stdClass::class): object|null
    {
        $stm = $this->pdo->prepare($sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        return self::fetchObject($stm, $class);
    }

    /**
     * @template TObject of object
     * @param array<int|string, mixed> $params
     * @param class-string<TObject> $class
     * @return iterable<mixed, TObject>
     */
    #[NoDiscard]
    public function objects(string $sql, array $params = [], string $class = stdClass::class): iterable
    {
        $stm = $this->pdo->prepare($sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        yield from self::fetchObjects($stm, $class);
    }

    public function rollback(): void
    {
        $ok = $this->pdo->rollBack();

        if ($ok !== true) {
            throw new UnexpectedValueException('rollBack');
        }
    }

    /**
     * @param array<int|string, mixed> $params
     */
    public function run(Stringable|string $sql, array $params = []): void
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function scalar(Stringable|string $sql, array $params = []): bool|float|int|string|null
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $row = $stm->fetch(PDO::FETCH_NUM);

        if (!is_array($row)) {
            throw new UnexpectedValueException('fetch');
        }

        if (!array_key_exists(0, $row)) {
            throw new UnexpectedValueException('$row');
        }

        $scalar = $row[0];

        if (is_scalar($scalar) || $scalar === null) {
            return $scalar;
        }

        $scalar = filter_var($scalar, FILTER_DEFAULT);

        if (!is_scalar($scalar) && $scalar !== null) {
            throw new UnexpectedValueException('$scalar');
        }

        return $scalar;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    #[NoDiscard]
    public function string(Stringable|string $sql, array $params = []): string
    {
        $stm = $this->pdo->prepare((string) $sql);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $ok = $stm->execute($params);

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        $row = $stm->fetch(PDO::FETCH_NUM);

        if (!is_array($row)) {
            throw new UnexpectedValueException('fetch');
        }

        if (!array_key_exists(0, $row)) {
            throw new UnexpectedValueException('$row');
        }

        $string = $row[0];

        if (is_string($string)) {
            return $string;
        }

        $string = filter_var($string, FILTER_DEFAULT);

        if (!is_string($string)) {
            throw new UnexpectedValueException('$string');
        }

        return $string;
    }

    /**
     * @template TObject of object
     * @param class-string<TObject> $class
     * @return TObject|null
     */
    #[NoDiscard]
    private static function fetchObject(PDOStatement $stm, string $class): object|null
    {
        $object = $stm->fetchObject($class);

        if ($object === false) {
            return null;
        }

        assert($object instanceof $class);

        return $object;
    }

    /**
     * @template TObject of object
     * @param class-string<TObject> $class
     * @return iterable<mixed, TObject>
     */
    #[NoDiscard]
    private static function fetchObjects(PDOStatement $stm, string $class): iterable
    {
        while (true) {
            $object = self::fetchObject($stm, $class);

            if ($object === null) {
                return;
            }

            yield $object;
        }
    }
}
