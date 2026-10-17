<?php

namespace App\Services\Catalog;

final class JsonCatalogImportReport
{
    /** @var list<string> */
    public array $errors = [];

    /** @var list<string> */
    public array $notices = [];

    public int $productsToImport = 0;

    public int $productsCreated = 0;

    public int $productsUpdated = 0;

    /** @var list<array{slug: string, name: string}> */
    public array $categoriesToCreate = [];

    public int $categoriesCreated = 0;

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function addNotice(string $message): void
    {
        $this->notices[] = $message;
    }

    public function isBlocked(): bool
    {
        return $this->errors !== [];
    }
}
