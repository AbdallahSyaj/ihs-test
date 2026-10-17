<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductsPageTest extends TestCase
{
    public function test_products_page_returns_ok_with_server_rendered_catalog(): void
    {
        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertSee('Explore Our Equipment', false);
        $response->assertSee('All Equipment', false);
        $response->assertSee('data-category="gold-detectors"', false);
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('class="product-card"', false);
        $response->assertSee('12 items', false);
        $response->assertSee(route('frontend.products.show', 'aurum-pro-x1'), false);
        $response->assertSee('badge-gold', false);
        $response->assertSee('New', false);
    }

    public function test_product_detail_route_still_resolves_for_catalog_slug(): void
    {
        $this->get(route('frontend.products.show', 'aurum-pro-x1'))
            ->assertOk()
            ->assertSee('Aurum Pro X1', false);
    }

    public function test_unknown_product_slug_still_returns_not_found(): void
    {
        $this->get(route('frontend.products.show', 'not-a-real-product'))
            ->assertNotFound();
    }
}
