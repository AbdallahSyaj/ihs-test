<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductInquiryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PublicStorefrontFixture;
use Tests\TestCase;

class CartPageTest extends TestCase
{
    use PublicStorefrontFixture;
    use RefreshDatabase;

    public function test_cart_page_renders_with_inquiry_form(): void
    {
        $response = $this->get(route('frontend.cart'));

        $response->assertOk();
        $response->assertSee('Equipment Inquiry', false);
        $response->assertSee('class="cart__title"', false);
        $response->assertSee('id="cart-inquiry-form"', false);
        $response->assertSee(route('frontend.cart.store'), false);
        $response->assertSee('frontend/css/pages/cart.css', false);
    }

    public function test_product_inquiry_submission_persists_request(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = $this->createStorefrontProductWithVariants($gold);

        $payload = [
            'name' => 'Jane Prospector',
            'email' => 'jane@example.com',
            'phone' => '+1 555 0100',
            'items' => [
                [
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'model' => $product->model,
                    'quantity' => 2,
                    'options' => ['Coil' => '11" DD'],
                ],
            ],
        ];

        $response = $this->postJson(route('frontend.cart.store'), $payload);

        $response->assertOk();
        $response->assertJsonPath('message', 'Thank you — your equipment inquiry has been received. We will contact you shortly.');

        $this->assertDatabaseHas('product_inquiry_requests', [
            'email' => 'jane@example.com',
            'phone' => '+1 555 0100',
        ]);

        $stored = ProductInquiryRequest::query()->first();
        $this->assertNotNull($stored);
        $this->assertCount(1, $stored->items);
        $this->assertSame('aurum-pro-x1', $stored->items[0]['slug']);
        $this->assertSame(2, $stored->items[0]['quantity']);
    }

    public function test_product_inquiry_rejects_inactive_product(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = $this->createStorefrontProductWithVariants($gold);
        $product->update(['status' => Product::STATUS_INACTIVE]);

        $payload = [
            'name' => 'Jane Prospector',
            'email' => 'jane@example.com',
            'phone' => '+1 555 0100',
            'items' => [
                [
                    'slug' => 'aurum-pro-x1',
                    'name' => $product->name,
                    'quantity' => 1,
                ],
            ],
        ];

        $this->postJson(route('frontend.cart.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.slug']);
    }
}
