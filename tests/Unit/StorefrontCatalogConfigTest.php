<?php

namespace Tests\Unit;

use Tests\TestCase;

class StorefrontCatalogConfigTest extends TestCase
{
    public function test_catalog_driver_defaults_to_json(): void
    {
        $this->assertSame('json', config('catalog.driver'));
    }
}
