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
use Psr\Container\ContainerInterface;
use TomasChochola\Pdo\PdoQuery;

use function assert;

/**
 * @no-named-arguments
 */
readonly class MysqlProbe
{
    public readonly PdoQuery $query;

    public function __construct(PdoQuery $query)
    {
        $this->query = $query;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $query = $container->get(PdoQuery::class);

        assert($query instanceof PdoQuery);

        return new self($query);
    }

    #[NoDiscard]
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
            SQL, [$schema, $table, $column], );
    }

    #[NoDiscard]
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
            SQL, [$schema, $table, $foreignKey], );
    }

    #[NoDiscard]
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
            SQL, [$schema, $table, $index], );
    }

    #[NoDiscard]
    public function schema(string $schema): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`schemata`
                WHERE `schema_name` = ?
            )
            SQL, [$schema], );
    }

    #[NoDiscard]
    public function table(string $table, string|null $schema = null): bool
    {
        return $this->query->bool(<<<'SQL'
            SELECT EXISTS(
                SELECT 1
                FROM `information_schema`.`tables`
                WHERE `table_schema` = COALESCE(?, DATABASE())
                    AND `table_name` = ?
            )
            SQL, [$schema, $table], );
    }
}
