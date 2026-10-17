<?php

namespace App\Support\Migrations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ConfiguresProductActiveSlug
{
    public const SLUG_LENGTH = 191;

    private const ACTIVE_SLUG_INDEX = 'products_active_slug_unique';

    private const ADDITIONAL_PRICE_CHECK = 'product_option_values_additional_price_nonnegative';

    protected function assertActiveSlugDriverSupported(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (! in_array($driver, ['sqlite', 'mysql', 'mariadb'], true)) {
            throw new \RuntimeException(
                'Product catalog migrations require sqlite, mysql, or mariadb. Driver ['.$driver.'] is not supported.'
            );
        }
    }

    protected function assertActiveSlugEnvironmentSupported(): void
    {
        $this->assertActiveSlugDriverSupported();

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            CatalogDatabaseVersion::assertSupportsGeneratedColumns($driver, $this->sqliteVersion());

            return;
        }

        $versionString = (string) DB::selectOne('select version() as version')->version;
        CatalogDatabaseVersion::assertSupportsGeneratedColumns($driver, $versionString);
    }

    protected function assertAdditionalPriceCheckConstraintSupported(): void
    {
        $this->assertActiveSlugDriverSupported();

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint($driver, $this->sqliteVersion());

            return;
        }

        $versionString = (string) DB::selectOne('select version() as version')->version;
        CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint($driver, $versionString);
    }

    protected function addProductActiveSlugColumn(): void
    {
        $this->assertActiveSlugEnvironmentSupported();

        $driver = Schema::getConnection()->getDriverName();
        $length = self::SLUG_LENGTH;

        if ($driver === 'sqlite') {
            DB::statement(
                'ALTER TABLE products ADD COLUMN active_slug TEXT '
                .'GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN slug ELSE NULL END) STORED'
            );
            DB::statement(
                'CREATE UNIQUE INDEX '.self::ACTIVE_SLUG_INDEX.' ON products (active_slug)'
            );

            return;
        }

        $charset = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';

        DB::statement(
            "ALTER TABLE products ADD COLUMN active_slug VARCHAR({$length}) "
            ."CHARACTER SET {$charset} COLLATE {$collation} "
            .'GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN slug ELSE NULL END) STORED'
        );
        DB::statement(
            'CREATE UNIQUE INDEX '.self::ACTIVE_SLUG_INDEX.' ON products (active_slug)'
        );
    }

    protected function addAdditionalPriceCheckConstraint(): void
    {
        $this->assertAdditionalPriceCheckConstraintSupported();

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement(
                'ALTER TABLE product_option_values '
                .'ADD CONSTRAINT '.self::ADDITIONAL_PRICE_CHECK.' CHECK (additional_price >= 0)'
            );

            return;
        }

        DB::statement(
            'ALTER TABLE product_option_values '
            .'ADD CONSTRAINT '.self::ADDITIONAL_PRICE_CHECK.' CHECK (additional_price >= 0)'
        );
    }

    protected function dropProductActiveSlugColumn(): void
    {
        $this->assertActiveSlugDriverSupported();

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $version = $this->sqliteVersion();
            if (version_compare($version, CatalogDatabaseVersion::SQLITE_MIN_DROP_GENERATED_COLUMN, '<')) {
                throw new \RuntimeException(
                    'SQLite '.CatalogDatabaseVersion::SQLITE_MIN_DROP_GENERATED_COLUMN.' or newer is required to roll back product active_slug columns. Found ['.$version.'].'
                );
            }
            DB::statement('DROP INDEX IF EXISTS '.self::ACTIVE_SLUG_INDEX);
            DB::statement('ALTER TABLE products DROP COLUMN active_slug');

            return;
        }

        $this->assertActiveSlugEnvironmentSupported();

        DB::statement('DROP INDEX '.self::ACTIVE_SLUG_INDEX.' ON products');
        DB::statement('ALTER TABLE products DROP COLUMN active_slug');
    }

    private function sqliteVersion(): string
    {
        $row = DB::selectOne('select sqlite_version() as version');

        return (string) $row->version;
    }
}
