<?php

namespace App\Contracts\Catalog;

interface StorefrontCatalogProvider
{
    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    public function catalog(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function findProduct(string $slug): ?array;
}
