<?php

use App\Support\Migrations\ConfiguresProductActiveSlug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use ConfiguresProductActiveSlug;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('slug', self::SLUG_LENGTH);
            $table->string('name');
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->longText('long_description')->nullable();
            $table->text('compatibility')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->string('badge')->nullable();
            $table->string('status', 32)->default('active');
            $table->string('catalog_origin', 32);
            $table->boolean('import_sync_enabled')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        $this->addProductActiveSlugColumn();

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->text('path');
            $table->unsignedInteger('gallery_order')->nullable();
            $table->boolean('is_listing')->default(false);
            $table->string('source', 32);
            $table->boolean('catalog_managed')->default(true);
            $table->timestamps();
        });

        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('catalog_managed')->default(true);
            $table->timestamps();
        });

        Schema::create('product_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->string('qty');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('catalog_managed')->default(true);
            $table->timestamps();
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('catalog_managed')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'name']);
        });

        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('catalog_managed')->default(true);
            $table->boolean('price_locked')->default(false);
            $table->timestamps();

            $table->unique(['product_option_id', 'label']);
        });

        $this->addAdditionalPriceCheckConstraint();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_accessories');
        Schema::dropIfExists('product_specs');
        Schema::dropIfExists('product_images');

        $this->dropProductActiveSlugColumn();

        Schema::dropIfExists('products');
    }
};
