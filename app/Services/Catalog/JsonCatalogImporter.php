<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAccessory;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductSpec;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JsonCatalogImporter
{
    public const JSON_PATH = 'frontend/data/products.json';

    /** @var list<string> */
    public const AUTHORITATIVE_CATEGORY_IDS = [
        'gold-detectors',
        'metal-detectors',
        'prospecting',
        'search-coils',
        'batteries',
        'accessories',
    ];

    public function __construct(
        private readonly string $jsonPath = '',
    ) {}

    public function resolveJsonPath(): string
    {
        return $this->jsonPath !== ''
            ? $this->jsonPath
            : public_path(self::JSON_PATH);
    }

    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    public function loadCatalog(): array
    {
        $path = $this->resolveJsonPath();
        if (! is_file($path)) {
            throw new \RuntimeException('Catalog JSON file not found at ['.$path.'].');
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('Catalog JSON is not valid JSON.');
        }

        $categories = $decoded['categories'] ?? null;
        $products = $decoded['products'] ?? null;
        if (! is_array($categories) || ! is_array($products)) {
            throw new \RuntimeException('Catalog JSON must contain categories and products arrays.');
        }

        return [
            'categories' => array_values($categories),
            'products' => array_values($products),
        ];
    }

    public function preflight(): JsonCatalogImportReport
    {
        $report = new JsonCatalogImportReport();

        if (! Schema::hasTable('categories')) {
            $report->addError('Categories table does not exist. Run migrations first.');

            return $report;
        }

        if (! Schema::hasColumn('categories', 'slug')) {
            $report->addError('Categories table is missing the slug column required for JSON import mapping.');

            return $report;
        }

        if (! Schema::hasTable('products')) {
            $report->addError('Products table does not exist. Run migrations first.');

            return $report;
        }

        try {
            $catalog = $this->loadCatalog();
        } catch (\RuntimeException $exception) {
            $report->addError($exception->getMessage());

            return $report;
        }

        $jsonCategoryLabels = $this->validateJsonCategoryDefinitions($catalog['categories'], $report);
        if ($report->isBlocked()) {
            return $report;
        }

        $categoryMap = $this->resolveCategoryMap($jsonCategoryLabels, $report);
        if ($report->isBlocked()) {
            return $report;
        }

        $this->validateProducts($catalog['products'], $categoryMap, $report);

        return $report;
    }

    public function apply(): JsonCatalogImportReport
    {
        $report = $this->preflight();
        if ($report->isBlocked()) {
            return $report;
        }

        $catalog = $this->loadCatalog();

        DB::transaction(function () use ($catalog, $report): void {
            foreach ($report->categoriesToCreate as $planned) {
                Category::query()->create([
                    'name' => $planned['name'],
                    'slug' => $planned['slug'],
                ]);
                $report->categoriesCreated++;
            }

            $categoryMap = $this->loadCategoryMapFromDatabase();

            foreach ($catalog['products'] as $productRow) {
                $dto = $this->mapProductRow($productRow, $categoryMap);
                $existing = Product::query()->where('slug', $dto['slug'])->first();

                if ($existing) {
                    $this->updateImportableProduct($existing, $dto);
                    $report->productsUpdated++;
                } else {
                    $this->createImportedProduct($dto);
                    $report->productsCreated++;
                }
            }
        });

        return $report;
    }

    /**
     * @param  list<array<string, mixed>>  $categories
     * @return array<string, string>
     */
    private function validateJsonCategoryDefinitions(array $categories, JsonCatalogImportReport $report): array
    {
        $labels = [];
        foreach ($categories as $index => $row) {
            $id = $row['id'] ?? null;
            if (! is_string($id) || $id === '') {
                $report->addError('JSON category at index '.$index.' is missing a valid id.');

                continue;
            }
            if (isset($labels[$id])) {
                $report->addError('Duplicate JSON category id ['.$id.'].');
            }

            $label = $row['label'] ?? null;
            if (! is_string($label) || trim($label) === '') {
                $report->addError('JSON category ['.$id.'] is missing a valid label.');

                continue;
            }

            $labels[$id] = trim($label);
        }

        foreach (self::AUTHORITATIVE_CATEGORY_IDS as $requiredId) {
            if (! isset($labels[$requiredId])) {
                $report->addError('JSON catalog is missing required category id ['.$requiredId.'].');
            }
        }

        $unexpected = array_diff(array_keys($labels), self::AUTHORITATIVE_CATEGORY_IDS);
        foreach ($unexpected as $extraId) {
            $report->addError('JSON catalog contains unexpected category id ['.$extraId.'].');
        }

        return $labels;
    }

    /**
     * @param  array<string, string>  $jsonCategoryLabels
     * @return array<string, int|null>
     */
    private function resolveCategoryMap(array $jsonCategoryLabels, JsonCatalogImportReport $report): array
    {
        $map = [];

        foreach (self::AUTHORITATIVE_CATEGORY_IDS as $slug) {
            $label = $jsonCategoryLabels[$slug] ?? '';

            $active = Category::query()->where('slug', $slug)->get();
            if ($active->count() > 1) {
                $report->addError('Multiple active categories share slug ['.$slug.'].');
                continue;
            }

            $activeCategory = $active->first();
            $trashed = Category::onlyTrashed()->where('slug', $slug)->exists();

            if ($activeCategory !== null) {
                if ($trashed) {
                    $report->addError('Category slug ['.$slug.'] has both active and trashed rows; resolve before import.');
                    continue;
                }

                $map[$slug] = (int) $activeCategory->id;

                continue;
            }

            if ($trashed) {
                $report->addError('Category slug ['.$slug.'] exists only in trash; restore or recreate before import.');

                continue;
            }

            if ($this->hasAmbiguousCategoryLabel($slug, $label)) {
                $report->addError(
                    'Ambiguous category mapping for slug ['.$slug.']: an active category already uses the JSON label ['.$label.'] with a different slug.'
                );

                continue;
            }

            $report->categoriesToCreate[] = [
                'slug' => $slug,
                'name' => $label,
            ];
            $report->addNotice('Will create category ['.$slug.'] with name ['.$label.'].');
            $map[$slug] = null;
        }

        return $map;
    }

    private function hasAmbiguousCategoryLabel(string $requiredSlug, string $jsonLabel): bool
    {
        return Category::query()
            ->where('name', $jsonLabel)
            ->where('slug', '!=', $requiredSlug)
            ->exists();
    }

    /**
     * @return array<string, int>
     */
    private function loadCategoryMapFromDatabase(): array
    {
        $map = [];

        foreach (self::AUTHORITATIVE_CATEGORY_IDS as $slug) {
            $category = Category::query()->where('slug', $slug)->firstOrFail();
            $map[$slug] = (int) $category->id;
        }

        return $map;
    }

    /**
     * @param  list<array<string, mixed>>  $products
     * @param  array<string, int>  $categoryMap
     */
    private function validateProducts(array $products, array $categoryMap, JsonCatalogImportReport $report): void
    {
        if ($categoryMap === []) {
            return;
        }

        $seenSlugs = [];

        foreach ($products as $index => $row) {
            $jsonId = $row['id'] ?? null;
            if (! is_string($jsonId) || $jsonId === '') {
                $report->addError('Product at index '.$index.' is missing id.');
                continue;
            }

            if (isset($seenSlugs[$jsonId])) {
                $report->addError('Duplicate JSON product id/slug ['.$jsonId.'].');
                continue;
            }
            $seenSlugs[$jsonId] = true;

            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $jsonId)) {
                $report->addError('Product id ['.$jsonId.'] is not a valid slug.');
            }

            $name = $row['name'] ?? null;
            if (! is_string($name) || trim($name) === '') {
                $report->addError('Product ['.$jsonId.'] is missing name.');
            }

            $categorySlug = $row['category'] ?? null;
            if (! is_string($categorySlug) || ! array_key_exists($categorySlug, $categoryMap)) {
                $report->addError('Product ['.$jsonId.'] has invalid or unmapped category ['.(string) $categorySlug.'].');
            }

            $price = $this->parsePrice($row['price'] ?? null);
            if ($price === null) {
                $report->addError('Product ['.$jsonId.'] has invalid price.');
            }

            $listing = $row['image'] ?? null;
            if (! is_string($listing) || ! $this->isValidHttpUrl($listing)) {
                $report->addError('Product ['.$jsonId.'] has invalid listing image URL.');
            }

            $gallery = $row['gallery'] ?? [];
            if (! is_array($gallery)) {
                $report->addError('Product ['.$jsonId.'] gallery must be an array.');
            } else {
                foreach ($gallery as $galleryIndex => $url) {
                    if (! is_string($url) || ! $this->isValidHttpUrl($url)) {
                        $report->addError('Product ['.$jsonId.'] gallery item '.$galleryIndex.' is not a valid URL.');
                    }
                }
            }

            $this->validateNestedProductData($jsonId, $row, $report);
            $this->validateOwnershipConflicts($jsonId, $report);
        }

        $report->productsToImport = count($seenSlugs);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function validateNestedProductData(string $jsonId, array $row, JsonCatalogImportReport $report): void
    {
        $specs = $row['specs'] ?? [];
        if (! is_array($specs)) {
            $report->addError('Product ['.$jsonId.'] specs must be an array.');
        } else {
            foreach ($specs as $specIndex => $spec) {
                if (! is_array($spec) || ! filled($spec['label'] ?? null) || ! filled($spec['value'] ?? null)) {
                    $report->addError('Product ['.$jsonId.'] spec '.$specIndex.' is malformed.');
                }
            }
        }

        $accessories = $row['accessories'] ?? [];
        if (! is_array($accessories)) {
            $report->addError('Product ['.$jsonId.'] accessories must be an array.');
        } else {
            foreach ($accessories as $accessoryIndex => $accessory) {
                if (! is_array($accessory) || ! filled($accessory['name'] ?? null)) {
                    $report->addError('Product ['.$jsonId.'] accessory '.$accessoryIndex.' is malformed.');
                }
                if (isset($accessory['qty']) && ! is_string($accessory['qty']) && ! is_numeric($accessory['qty'])) {
                    $report->addError('Product ['.$jsonId.'] accessory '.$accessoryIndex.' qty must be a string.');
                }
            }
        }

        $variants = $row['variants'] ?? [];
        if (! is_array($variants)) {
            $report->addError('Product ['.$jsonId.'] variants must be an array.');
        } else {
            foreach ($variants as $variantIndex => $variant) {
                if (! is_array($variant) || ! filled($variant['name'] ?? null) || ! is_array($variant['options'] ?? null)) {
                    $report->addError('Product ['.$jsonId.'] variant '.$variantIndex.' is malformed.');
                    continue;
                }
                foreach ($variant['options'] as $optionIndex => $label) {
                    if (! is_string($label) || $label === '') {
                        $report->addError('Product ['.$jsonId.'] variant '.$variantIndex.' option '.$optionIndex.' is invalid.');
                    }
                }
            }
        }
    }

    private function validateOwnershipConflicts(string $slug, JsonCatalogImportReport $report): void
    {
        $product = Product::query()->where('slug', $slug)->first();
        if ($product === null) {
            return;
        }

        if ($product->catalog_origin === Product::ORIGIN_ADMIN) {
            $report->addError('Product slug ['.$slug.'] is owned by an admin-created product; import stopped.');

            return;
        }

        if ($this->productHasManualChildren($product)) {
            $report->addError('Product slug ['.$slug.'] has manually managed child records; import stopped.');
        }
    }

    private function productHasManualChildren(Product $product): bool
    {
        return $product->specs()->where('catalog_managed', false)->exists()
            || $product->accessories()->where('catalog_managed', false)->exists()
            || $product->options()->where('catalog_managed', false)->exists()
            || $product->images()->where('catalog_managed', false)->exists()
            || ProductOptionValue::query()
                ->whereIn('product_option_id', $product->options()->pluck('id'))
                ->where('catalog_managed', false)
                ->exists()
            || ProductOptionValue::query()
                ->whereIn('product_option_id', $product->options()->pluck('id'))
                ->where('price_locked', true)
                ->exists();
    }

    /**
     * @param  array<string, int>  $categoryMap
     * @return array<string, mixed>
     */
    private function mapProductRow(array $row, array $categoryMap): array
    {
        $slug = (string) $row['id'];

        return [
            'slug' => $slug,
            'category_id' => $categoryMap[(string) $row['category']],
            'name' => (string) $row['name'],
            'model' => isset($row['model']) ? (string) $row['model'] : null,
            'description' => isset($row['description']) ? (string) $row['description'] : null,
            'long_description' => isset($row['longDescription']) ? (string) $row['longDescription'] : null,
            'compatibility' => isset($row['compatibility']) ? (string) $row['compatibility'] : null,
            'base_price' => $this->parsePrice($row['price'] ?? null),
            'badge' => isset($row['badge']) ? (string) $row['badge'] : null,
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
            'specs' => $this->mapSpecs($row),
            'accessories' => $this->mapAccessories($row),
            'options' => $this->mapOptions($row),
            'images' => $this->mapImages($row),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<array<string, mixed>>
     */
    private function mapSpecs(array $row): array
    {
        $specs = [];
        foreach (array_values($row['specs'] ?? []) as $index => $spec) {
            if (! is_array($spec)) {
                continue;
            }
            $specs[] = [
                'label' => (string) $spec['label'],
                'value' => (string) $spec['value'],
                'sort_order' => $index,
            ];
        }

        return $specs;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<array<string, mixed>>
     */
    private function mapAccessories(array $row): array
    {
        $rows = [];
        foreach (array_values($row['accessories'] ?? []) as $index => $accessory) {
            if (! is_array($accessory)) {
                continue;
            }
            $rows[] = [
                'name' => (string) $accessory['name'],
                'qty' => isset($accessory['qty']) ? (string) $accessory['qty'] : '',
                'sort_order' => $index,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<array<string, mixed>>
     */
    private function mapOptions(array $row): array
    {
        $options = [];
        foreach (array_values($row['variants'] ?? []) as $optionIndex => $variant) {
            if (! is_array($variant)) {
                continue;
            }
            $values = [];
            foreach (array_values($variant['options'] ?? []) as $valueIndex => $label) {
                $values[] = [
                    'label' => (string) $label,
                    'additional_price' => '0.00',
                    'sort_order' => $valueIndex,
                ];
            }
            $options[] = [
                'name' => (string) $variant['name'],
                'sort_order' => $optionIndex,
                'values' => $values,
            ];
        }

        return $options;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{listing: string, gallery: list<string>}
     */
    private function mapImages(array $row): array
    {
        return [
            'listing' => (string) $row['image'],
            'gallery' => array_values(array_map(
                static fn ($url) => (string) $url,
                is_array($row['gallery'] ?? null) ? $row['gallery'] : []
            )),
        ];
    }

    /**
     * @param  array<string, mixed>  $dto
     */
    private function createImportedProduct(array $dto): void
    {
        $product = Product::query()->create([
            'category_id' => $dto['category_id'],
            'slug' => $dto['slug'],
            'name' => $dto['name'],
            'model' => $dto['model'],
            'description' => $dto['description'],
            'long_description' => $dto['long_description'],
            'compatibility' => $dto['compatibility'],
            'base_price' => $dto['base_price'],
            'badge' => $dto['badge'],
            'status' => $dto['status'],
            'catalog_origin' => $dto['catalog_origin'],
            'import_sync_enabled' => true,
        ]);

        $this->syncImportedChildren($product, $dto);
    }

    /**
     * @param  array<string, mixed>  $dto
     */
    private function updateImportableProduct(Product $product, array $dto): void
    {
        $product->update([
            'category_id' => $dto['category_id'],
            'name' => $dto['name'],
            'model' => $dto['model'],
            'description' => $dto['description'],
            'long_description' => $dto['long_description'],
            'compatibility' => $dto['compatibility'],
            'base_price' => $dto['base_price'],
            'badge' => $dto['badge'],
            'import_sync_enabled' => true,
        ]);

        $this->syncImportedChildren($product, $dto);
    }

    /**
     * @param  array<string, mixed>  $dto
     */
    private function syncImportedChildren(Product $product, array $dto): void
    {
        $product->specs()->where('catalog_managed', true)->delete();
        foreach ($dto['specs'] as $spec) {
            $product->specs()->create([
                'label' => $spec['label'],
                'value' => $spec['value'],
                'sort_order' => $spec['sort_order'],
                'catalog_managed' => true,
            ]);
        }

        $product->accessories()->where('catalog_managed', true)->delete();
        foreach ($dto['accessories'] as $accessory) {
            $product->accessories()->create([
                'name' => $accessory['name'],
                'qty' => $accessory['qty'],
                'sort_order' => $accessory['sort_order'],
                'catalog_managed' => true,
            ]);
        }

        $optionIds = $product->options()->where('catalog_managed', true)->pluck('id');
        ProductOptionValue::query()->whereIn('product_option_id', $optionIds)->where('catalog_managed', true)->delete();
        $product->options()->where('catalog_managed', true)->delete();

        foreach ($dto['options'] as $optionRow) {
            $option = $product->options()->create([
                'name' => $optionRow['name'],
                'sort_order' => $optionRow['sort_order'],
                'catalog_managed' => true,
            ]);
            foreach ($optionRow['values'] as $valueRow) {
                $option->values()->create([
                    'label' => $valueRow['label'],
                    'additional_price' => $valueRow['additional_price'],
                    'sort_order' => $valueRow['sort_order'],
                    'catalog_managed' => true,
                    'price_locked' => false,
                ]);
            }
        }

        $product->images()
            ->where('catalog_managed', true)
            ->where('source', ProductImage::SOURCE_IMPORT)
            ->delete();

        $product->images()->create([
            'path' => $dto['images']['listing'],
            'gallery_order' => null,
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);

        foreach ($dto['images']['gallery'] as $order => $url) {
            $product->images()->create([
                'path' => $url,
                'gallery_order' => $order,
                'is_listing' => false,
                'source' => ProductImage::SOURCE_IMPORT,
                'catalog_managed' => true,
            ]);
        }
    }

    private function parsePrice(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $normalized = preg_replace('/[^\d.]/', '', (string) $value);
        if ($normalized === null || $normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        return number_format((float) $normalized, 2, '.', '');
    }

    private function isValidHttpUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'));
    }
}
