<?php

namespace Tests\Feature;

use App\Mail\ProductInquiryReplyMailable;
use App\Models\Admin;
use App\Models\MailOutboundMessage;
use App\Models\Product;
use App\Models\ProductInquiryRequest;
use App\Services\Mail\ProductInquiryReplyMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Support\PublicStorefrontFixture;
use Tests\TestCase;

class ProductInquiryAdminTest extends TestCase
{
    use PublicStorefrontFixture;
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function inquiryPayload(Product $product): array
    {
        return [
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
    }

    private function createInquiry(): ProductInquiryRequest
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = $this->createStorefrontProductWithVariants($gold);

        $this->postJson(route('frontend.cart.store'), $this->inquiryPayload($product))->assertOk();

        return ProductInquiryRequest::query()->firstOrFail();
    }

    public function test_guest_cannot_access_product_inquiry_admin(): void
    {
        $inquiry = $this->createInquiry();

        $this->get(route('super_admin.product_inquiry_requests-index'))->assertRedirect(route('login'));
        $this->get(route('super_admin.product_inquiry_requests-show', $inquiry->id))->assertRedirect(route('login'));
        $this->post(route('super_admin.product_inquiry_requests-reply', $inquiry->id), [
            'subject' => 'Test',
            'body' => 'Hello',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_list_and_view_inquiry_details(): void
    {
        $inquiry = $this->createInquiry();
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.product_inquiry_requests-index'))
            ->assertOk()
            ->assertSee('Jane Prospector', false)
            ->assertSee('jane@example.com', false);

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.product_inquiry_requests-show', $inquiry->id))
            ->assertOk()
            ->assertSee('Jane Prospector', false)
            ->assertSee('jane@example.com', false)
            ->assertSee('+1 555 0100', false)
            ->assertSee('Aurum Pro X1', false)
            ->assertSee('Coil: 11&quot; DD', false)
            ->assertSee('2', false);

        $inquiry->refresh();
        $this->assertNotNull($inquiry->read_at);
    }

    public function test_reply_validation_requires_subject_and_body(): void
    {
        $inquiry = $this->createInquiry();
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.product_inquiry_requests-show', $inquiry->id))
            ->post(route('super_admin.product_inquiry_requests-reply', $inquiry->id), [])
            ->assertRedirect(route('super_admin.product_inquiry_requests-show', $inquiry->id))
            ->assertSessionHasErrors(['subject', 'body']);

        $this->assertDatabaseCount('mail_outbound_messages', 0);
    }

    public function test_successful_reply_sends_to_inquiry_email_and_marks_sent(): void
    {
        Mail::fake();

        $inquiry = $this->createInquiry();
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.product_inquiry_requests-reply', $inquiry->id), [
                'subject' => 'Regarding your equipment inquiry',
                'body' => 'We can supply the requested models.',
            ])
            ->assertRedirect(route('super_admin.product_inquiry_requests-show', $inquiry->id))
            ->assertSessionHas('success');

        $outbound = MailOutboundMessage::query()->first();
        $this->assertNotNull($outbound);
        $this->assertSame('jane@example.com', $outbound->recipient_email);
        $this->assertSame(MailOutboundMessage::STATUS_SENT, $outbound->status);
        $this->assertNotNull($outbound->sent_at);

        Mail::assertSent(ProductInquiryReplyMailable::class, function (ProductInquiryReplyMailable $mail) {
            return $mail->replySubject === 'Regarding your equipment inquiry'
                && str_contains($mail->replyBody, 'requested models');
        });

        $inquiry->refresh();
        $this->assertSame(ProductInquiryRequest::STATUS_REPLIED, $inquiry->status);
    }

    public function test_failed_reply_persists_failed_status_and_does_not_mark_sent(): void
    {
        $inquiry = $this->createInquiry();
        $admin = Admin::factory()->create();

        $this->mock(ProductInquiryReplyMailer::class, function ($mock): void {
            $mock->shouldReceive('send')
                ->once()
                ->andThrow(new \RuntimeException('Transport connection refused'));
        });

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.product_inquiry_requests-reply', $inquiry->id), [
                'subject' => 'Follow up',
                'body' => 'Checking availability.',
            ])
            ->assertRedirect(route('super_admin.product_inquiry_requests-show', $inquiry->id))
            ->assertSessionHas('danger');

        $outbound = MailOutboundMessage::query()->first();
        $this->assertNotNull($outbound);
        $this->assertSame(MailOutboundMessage::STATUS_FAILED, $outbound->status);
        $this->assertNull($outbound->sent_at);
        $this->assertNotNull($outbound->failed_at);
        $this->assertStringContainsString('Transport connection refused', (string) $outbound->error_message);
    }

    public function test_cart_submission_remains_compatible(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = $this->createStorefrontProductWithVariants($gold);

        $this->postJson(route('frontend.cart.store'), $this->inquiryPayload($product))
            ->assertOk()
            ->assertJsonPath('message', 'Thank you — your equipment inquiry has been received. We will contact you shortly.');

        $stored = ProductInquiryRequest::query()->first();
        $this->assertNotNull($stored);
        $this->assertSame('new', $stored->status);
        $this->assertCount(1, $stored->items);
    }
}
