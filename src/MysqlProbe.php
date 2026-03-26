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

use NoDiscard;
use Override;
use TomasChochola\Pdo\ProbeInterface;
use TomasChochola\Pdo\QueryInterface;

/**
 * @no-named-arguments
 */
readonly class MysqlProbe implements ProbeInterface
{
    public readonly QueryInterface $query;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    #[NoDiscard]
    #[Override]
    public function column(string $table, string $column, string|null $schema = null): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`columns`
                WHERE `table_schema` = COALESCE(?, DATABASE())
                    AND `table_name` = ?
                    AND `column_name` = ?
            )
            SQL, [$schema, $table, $column]);
    }

    #[NoDiscard]
    #[Override]
    public function foreign(string $table, string $foreignKey, string|null $schema = null): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`table_constraints`
                WHERE `table_schema` = COALESCE(?, DATABASE())
                    AND `table_name` = ?
                    AND `constraint_name` = ?
                    AND `constraint_type` = 'FOREIGN KEY'
            )
            SQL, [$schema, $table, $foreignKey]);
    }

    #[NoDiscard]
    #[Override]
    public function index(string $table, string $index, string|null $schema = null): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`statistics`
                WHERE `table_schema` = COALESCE(?, DATABASE())
                    AND `table_name` = ?
                    AND `index_name` = ?
            )
            SQL, [$schema, $table, $index]);
    }

    #[NoDiscard]
    #[Override]
    public function schema(string $schema): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`schemata`
                WHERE `schema_name` = ?
            )
            SQL, [$schema]);
    }

    #[NoDiscard]
    #[Override]
    public function table(string $table, string|null $schema = null): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`tables`
                WHERE `table_schema` = COALESCE(?, DATABASE())
                    AND `table_name` = ?
            )
            SQL, [$schema, $table]);
    }
}
