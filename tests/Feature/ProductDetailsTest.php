<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
    public function test_valid_product_renders_full_details_in_html(): void
    {
        $response = $this->get(route('frontend.products.show', 'aurum-pro-x1'));

        $response->assertOk();
        $response->assertSee('class="pd__title"', false);
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('Pulse Induction', false);
        $response->assertSee('data-tab-panel="description"', false);
        $response->assertSee('data-tab-panel="specs"', false);
        $response->assertSee('data-tab-panel="accessories"', false);
        $response->assertSee('data-tab-panel="compatibility"', false);
        $response->assertSee('data-variant="Coil Size"', false);
        $response->assertSee('badge-gold', false);
        $response->assertSee(route('frontend.contact'), false);
        $response->assertSee(route('frontend.products'), false);
    }

    public function test_product_without_variants_omits_variant_interface(): void
    {
        $response = $this->get(route('frontend.products.show', 'aurum-battery-pack'));

        $response->assertOk();
        $response->assertSee('Aurum Battery Pack', false);
        $response->assertDontSee('id="pd-variants"', false);
    }

    public function test_invalid_product_slug_returns_not_found(): void
    {
        $this->get(route('frontend.products.show', 'not-a-real-product'))
            ->assertNotFound();
    }
}
