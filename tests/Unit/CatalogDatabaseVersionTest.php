<?php

namespace Tests\Unit;

use App\Support\Migrations\CatalogDatabaseVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatalogDatabaseVersionTest extends TestCase
{
    #[DataProvider('serverVersionProvider')]
    public function test_parse_server_version(string $versionString, string $engine, string $version): void
    {
        $parsed = CatalogDatabaseVersion::parseServerVersion($versionString);

        $this->assertSame($engine, $parsed['engine']);
        $this->assertSame($version, $parsed['version']);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function serverVersionProvider(): array
    {
        return [
            'mysql 8.0.x' => ['8.0.36', 'mysql', '8.0.36'],
            'mysql 5.7.x' => ['5.7.44', 'mysql', '5.7.44'],
            'mariadb prefixed' => ['5.5.5-10.11.6-MariaDB', 'mariadb', '10.11.6'],
            'mariadb 10.1.x' => ['10.1.48-MariaDB', 'mariadb', '10.1.48'],
            'mariadb 10.2.1' => ['10.2.1-MariaDB', 'mariadb', '10.2.1'],
        ];
    }

    public function test_mysql_8_passes_generated_column_and_check_gates(): void
    {
        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mysql', '8.0.36');
        CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint('mysql', '8.0.36');

        $this->assertTrue(true);
    }

    public function test_mysql_5_7_passes_generated_column_gate_but_fails_check_gate(): void
    {
        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mysql', '5.7.44');

        $this->expectException(\RuntimeException::class);
        CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint('mysql', '5.7.44');
    }

    public function test_mariadb_10_1_fails_generated_column_gate_when_using_mysql_driver(): void
    {
        $this->expectException(\RuntimeException::class);

        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mysql', '10.1.48-MariaDB');
    }

    public function test_mariadb_10_1_fails_generated_column_gate_when_using_mariadb_driver(): void
    {
        $this->expectException(\RuntimeException::class);

        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mariadb', '10.1.48-MariaDB');
    }

    public function test_mariadb_10_2_1_passes_gates_with_mysql_driver(): void
    {
        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mysql', '10.2.1-MariaDB');
        CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint('mysql', '10.2.1-MariaDB');

        $this->assertTrue(true);
    }

    public function test_mariadb_prefixed_version_passes_gates_with_mysql_driver(): void
    {
        CatalogDatabaseVersion::assertSupportsGeneratedColumns('mysql', '5.5.5-10.11.6-MariaDB');
        CatalogDatabaseVersion::assertSupportsAdditionalPriceCheckConstraint('mysql', '5.5.5-10.11.6-MariaDB');

        $this->assertTrue(true);
    }
}
