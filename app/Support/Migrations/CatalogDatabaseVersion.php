<?php

namespace App\Support\Migrations;

final class CatalogDatabaseVersion
{
    public const MYSQL_MIN_GENERATED_COLUMNS = '5.7.6';

    public const MYSQL_MIN_CHECK_CONSTRAINT = '8.0.16';

    public const MARIADB_MIN = '10.2.1';

    public const SQLITE_MIN_GENERATED_COLUMNS = '3.31.0';

    public const SQLITE_MIN_DROP_GENERATED_COLUMN = '3.35.0';

    public const SQLITE_MIN_CHECK_CONSTRAINT = '3.37.0';

    /**
     * @return array{engine: 'mysql'|'mariadb', version: string}
     */
    public static function parseServerVersion(string $versionString): array
    {
        if (stripos($versionString, 'mariadb') !== false) {
            if (preg_match('/-(\d+\.\d+\.\d+)-MariaDB/i', $versionString, $matches) === 1) {
                return ['engine' => 'mariadb', 'version' => $matches[1]];
            }

            if (preg_match('/(\d+\.\d+\.\d+)-MariaDB/i', $versionString, $matches) === 1) {
                return ['engine' => 'mariadb', 'version' => $matches[1]];
            }

            if (preg_match('/(\d+\.\d+\.\d+)/', $versionString, $matches) === 1) {
                return ['engine' => 'mariadb', 'version' => $matches[1]];
            }

            throw new \RuntimeException(
                'Unable to parse MariaDB server version from ['.$versionString.'].'
            );
        }

        if (preg_match('/(\d+\.\d+\.\d+)/', $versionString, $matches) === 1) {
            return ['engine' => 'mysql', 'version' => $matches[1]];
        }

        if (preg_match('/(\d+\.\d+)/', $versionString, $matches) === 1) {
            return ['engine' => 'mysql', 'version' => $matches[1]];
        }

        throw new \RuntimeException(
            'Unable to parse MySQL server version from ['.$versionString.'].'
        );
    }

    public static function assertSupportsGeneratedColumns(string $driver, string $versionString): void
    {
        if ($driver === 'sqlite') {
            if (version_compare($versionString, self::SQLITE_MIN_GENERATED_COLUMNS, '<')) {
                throw new \RuntimeException(
                    'SQLite '.self::SQLITE_MIN_GENERATED_COLUMNS.' or newer is required for product active_slug generated columns. Found ['.$versionString.'].'
                );
            }

            return;
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException(
                'Product catalog migrations require sqlite, mysql, or mariadb. Driver ['.$driver.'] is not supported.'
            );
        }

        $parsed = self::parseServerVersion($versionString);

        if ($parsed['engine'] === 'mariadb') {
            if (version_compare($parsed['version'], self::MARIADB_MIN, '<')) {
                throw new \RuntimeException(
                    'MariaDB '.self::MARIADB_MIN.' or newer is required for product catalog migrations. Found ['.$versionString.'].'
                );
            }

            return;
        }

        if (version_compare($parsed['version'], self::MYSQL_MIN_GENERATED_COLUMNS, '<')) {
            throw new \RuntimeException(
                'MySQL '.self::MYSQL_MIN_GENERATED_COLUMNS.' or newer is required for product active_slug generated columns. Found ['.$versionString.'].'
            );
        }
    }

    public static function assertSupportsAdditionalPriceCheckConstraint(string $driver, string $versionString): void
    {
        if ($driver === 'sqlite') {
            if (version_compare($versionString, self::SQLITE_MIN_CHECK_CONSTRAINT, '<')) {
                throw new \RuntimeException(
                    'SQLite '.self::SQLITE_MIN_CHECK_CONSTRAINT.' or newer is required to enforce additional_price CHECK constraints. Found ['.$versionString.'].'
                );
            }

            return;
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException(
                'Product catalog migrations require sqlite, mysql, or mariadb. Driver ['.$driver.'] is not supported.'
            );
        }

        $parsed = self::parseServerVersion($versionString);

        if ($parsed['engine'] === 'mariadb') {
            if (version_compare($parsed['version'], self::MARIADB_MIN, '<')) {
                throw new \RuntimeException(
                    'MariaDB '.self::MARIADB_MIN.' or newer is required to enforce additional_price CHECK constraints. Found ['.$versionString.'].'
                );
            }

            return;
        }

        if (version_compare($parsed['version'], self::MYSQL_MIN_CHECK_CONSTRAINT, '<')) {
            throw new \RuntimeException(
                'MySQL '.self::MYSQL_MIN_CHECK_CONSTRAINT.' or newer is required to enforce additional_price CHECK constraints. '
                .'MySQL 5.7 accepts CHECK syntax but does not enforce it. Found ['.$versionString.'].'
            );
        }
    }
}
