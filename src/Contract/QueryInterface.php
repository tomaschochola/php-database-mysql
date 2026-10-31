<?php

declare(strict_types=1);

namespace TomasChochola\Database\Mysql\Contract;

use NoDiscard;
use Stringable;
use stdClass;

/**
 * @no-named-arguments
 */
interface QueryInterface
{
    public function begin(): void;

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    public function bool(Stringable | string $sql, array $params = []): bool;

    public function commit(): void;

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    public function execute(Stringable | string $sql, array $params = []): int;

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    public function insert(string $sql, array $params = []): string;

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    public function int(Stringable | string $sql, array $params = []): int;

    /**
     * @template TObject of object
     *
     * @param array<mixed, mixed> $params
     * @param class-string<TObject> $class
     *
     * @return TObject|null
     */
    #[NoDiscard()]
    public function object(string $sql, array $params = [], string $class = stdClass::class): object | null;

    /**
     * @template TObject of object
     *
     * @param array<mixed, mixed> $params
     * @param class-string<TObject> $class
     *
     * @return iterable<mixed, TObject>
     */
    #[NoDiscard()]
    public function objects(string $sql, array $params = [], string $class = stdClass::class): iterable;

    public function rollback(): void;

    /**
     * @param array<mixed, mixed> $params
     */
    public function run(Stringable | string $sql, array $params = []): void;

    /**
     * @param array<mixed, mixed> $params
     */
    #[NoDiscard()]
    public function string(Stringable | string $sql, array $params = []): string;
}
