<?php

namespace App\Console\Commands;

use App\Services\Catalog\JsonCatalogImporter;
use Illuminate\Console\Command;

class ImportJsonCatalogCommand extends Command
{
    protected $signature = 'catalog:import-json
                            {--dry-run : Validate and report the planned import without writing}
                            {--apply : Apply the import to the database}';

    protected $description = 'Import the storefront JSON catalog into the database schema';

    public function handle(JsonCatalogImporter $importer): int
    {
        $apply = (bool) $this->option('apply');

        if (! $apply) {
            $report = $importer->preflight();
            $this->renderReport($report, applied: false);

            return $report->isBlocked() ? self::FAILURE : self::SUCCESS;
        }

        $report = $importer->apply();
        $this->renderReport($report, applied: true);

        return $report->isBlocked() ? self::FAILURE : self::SUCCESS;
    }

    private function renderReport(\App\Services\Catalog\JsonCatalogImportReport $report, bool $applied): void
    {
        if ($report->isBlocked()) {
            $this->error('Import blocked. No database writes were performed.');
            foreach ($report->errors as $error) {
                $this->line(' - '.$error);
            }

            return;
        }

        $this->info($applied
            ? 'Import completed successfully.'
            : 'Dry-run passed. No database writes were performed.');

        if ($report->categoriesToCreate !== []) {
            $this->line('Categories to create: '.count($report->categoriesToCreate));
            foreach ($report->categoriesToCreate as $category) {
                $this->line(' - '.$category['slug'].' ('.$category['name'].')');
            }
        }

        $this->line('Products planned: '.$report->productsToImport);

        if ($applied) {
            if ($report->categoriesCreated > 0) {
                $this->line('Categories created: '.$report->categoriesCreated);
            }
            $this->line('Products created: '.$report->productsCreated);
            $this->line('Products updated: '.$report->productsUpdated);
        }

        foreach ($report->notices as $notice) {
            $this->line($notice);
        }
    }
}
