<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ContactUs;
use App\Models\ContactUsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_submission_without_phone_stores_null(): void
    {
        $response = $this->postJson(route('frontend.contact.store'), $this->validPayload());

        $response->assertOk()->assertJson([
            'message' => "Thank you — we'll be in touch shortly.",
        ]);

        $this->assertDatabaseCount('contact_us_requests', 1);
        $this->assertNull(ContactUsRequest::query()->first()->phone);
    }

    public function test_invalid_email_is_rejected_and_creates_no_record(): void
    {
        $payload = $this->validPayload();
        $payload['email'] = 'not-an-email';

        $this->postJson(route('frontend.contact.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('contact_us_requests', 0);
    }

    public function test_missing_required_fields_are_rejected_and_create_no_record(): void
    {
        $this->postJson(route('frontend.contact.store'), [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);

        $this->assertDatabaseCount('contact_us_requests', 0);
    }

    public function test_non_json_failure_keeps_input_and_success_redirects(): void
    {
        $this->from(route('frontend.contact'))
            ->post(route('frontend.contact.store'), [
                'name' => 'Amina Saleh',
                'email' => 'not-an-email',
                'subject' => 'Coil compatibility',
                'message' => 'Which coil fits the Aurum Pro X1?',
            ])
            ->assertRedirect(route('frontend.contact'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('contact_us_requests', 0);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('value="Amina Saleh"', false)
            ->assertSee('not-an-email', false);

        $this->post(route('frontend.contact.store'), $this->validPayload())
            ->assertRedirect(route('frontend.contact'))
            ->assertSessionHas('contact_success', "Thank you — we'll be in touch shortly.");
    }

    public function test_optional_phone_is_stored_when_provided(): void
    {
        $payload = $this->validPayload();
        $payload['phone'] = '5551234567';

        $this->postJson(route('frontend.contact.store'), $payload)->assertOk();

        $this->assertSame('5551234567', ContactUsRequest::query()->first()->phone);
    }

    public function test_guest_cannot_update_contact_information(): void
    {
        $contact = $this->contactRecord();

        $this->post(route('super_admin.contact_us-update', $contact->id), $this->adminPayload())
            ->assertRedirect(route('login'));

        $this->assertNull($contact->fresh()->hours);
    }

    public function test_admin_can_save_business_hours_with_scheme_less_social_urls(): void
    {
        $admin = Admin::factory()->create();
        $contact = $this->contactRecord();

        $payload = $this->adminPayload();
        $payload['hours'] = "Sunday–Thursday\n9:00–18:00";
        $payload['facebook'] = 'www.facebook.com';

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.contact_us-update', $contact->id), $payload)
            ->assertRedirect(route('super_admin.contact_us-index'));

        $this->assertSame("Sunday–Thursday\n9:00–18:00", $contact->fresh()->hours);
        $this->assertSame('www.facebook.com', $contact->fresh()->facebook);
        $this->assertSame('1234567890', $contact->fresh()->phone);
    }

    public function test_admin_update_still_requires_the_business_phone(): void
    {
        $admin = Admin::factory()->create();
        $contact = $this->contactRecord(['hours' => 'Existing hours']);

        $payload = $this->adminPayload();
        unset($payload['phone']);

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.contact_us-edit', $contact->id))
            ->post(route('super_admin.contact_us-update', $contact->id), $payload)
            ->assertRedirect(route('super_admin.contact_us-edit', $contact->id))
            ->assertSessionHasErrors('phone');

        $this->assertSame('Existing hours', $contact->fresh()->hours);
    }

    public function test_public_page_displays_saved_hours_with_line_breaks_and_escaping(): void
    {
        $this->contactRecord([
            'hours' => "Line one\n<script>alert(1)</script>",
        ]);

        $response = $this->get(route('frontend.contact'));

        $response->assertOk();
        $response->assertSee('Line one', false);
        $response->assertSee('<br', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertDontSee('[Operating hours placeholder]', false);
    }

    public function test_blank_or_missing_hours_keep_the_placeholder(): void
    {
        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('[Operating hours placeholder]', false);

        $contact = $this->contactRecord(['hours' => '   ']);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('[Operating hours placeholder]', false);

        $contact->update(['hours' => null]);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('[Operating hours placeholder]', false);
    }

    public function test_admin_can_save_page_title_and_introductory_description(): void
    {
        $admin = Admin::factory()->create();
        $contact = $this->contactRecord();

        $payload = $this->adminPayload();
        $payload['page_title_en'] = 'Custom Contact Headline';
        $payload['page_intro_en'] = "Line one\nLine two";

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.contact_us-update', $contact->id), $payload)
            ->assertRedirect(route('super_admin.contact_us-index'));

        $fresh = $contact->fresh();
        $this->assertSame('Custom Contact Headline', $fresh->page_title_en);
        $this->assertSame("Line one\nLine two", $fresh->page_intro_en);
    }

    public function test_public_page_displays_saved_page_title_and_intro(): void
    {
        $this->contactRecord([
            'page_title_en' => 'Reach Our Team',
            'page_intro_en' => 'Tell us what you need and we will guide you.',
        ]);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee('Reach Our Team', false)
            ->assertSee('Tell us what you need and we will guide you.', false)
            ->assertSee('gold-text', false)
            ->assertSee('Equipment.', false);
    }

    public function test_blank_or_missing_page_copy_uses_hardcoded_fallbacks(): void
    {
        $defaultTitle = "Let's Find the Right";
        $defaultIntro = 'Questions about a product, equipment compatibility, or which detector suits your terrain?';

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee($defaultTitle, false)
            ->assertSee($defaultIntro, false)
            ->assertSee('Equipment.', false);

        $contact = $this->contactRecord([
            'page_title_en' => '   ',
            'page_intro_en' => " \t\n ",
        ]);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee($defaultTitle, false)
            ->assertSee($defaultIntro, false);

        $contact->update(['page_title_en' => null, 'page_intro_en' => null]);

        $this->get(route('frontend.contact'))
            ->assertOk()
            ->assertSee($defaultTitle, false)
            ->assertSee($defaultIntro, false);
    }

    public function test_admin_whitespace_page_copy_is_stored_as_null(): void
    {
        $admin = Admin::factory()->create();
        $contact = $this->contactRecord([
            'page_title_en' => 'Saved title',
            'page_intro_en' => 'Saved intro',
        ]);

        $payload = $this->adminPayload();
        $payload['page_title_en'] = '   ';
        $payload['page_intro_en'] = '  ';

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.contact_us-update', $contact->id), $payload)
            ->assertRedirect(route('super_admin.contact_us-index'));

        $fresh = $contact->fresh();
        $this->assertNull($fresh->page_title_en);
        $this->assertNull($fresh->page_intro_en);
    }

    public function test_public_page_escapes_page_intro_html(): void
    {
        $this->contactRecord([
            'page_intro_en' => '<script>alert(1)</script>',
        ]);

        $response = $this->get(route('frontend.contact'));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(): array
    {
        return [
            'name' => 'Amina Saleh',
            'email' => 'amina@example.com',
            'subject' => 'Coil compatibility',
            'message' => 'Which coil fits the Aurum Pro X1?',
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function contactRecord(array $overrides = []): ContactUs
    {
        return ContactUs::query()->create(array_merge([
            'email' => 'test@brws.com',
            'phone' => '1234567890',
            'address_ar' => 'عنوان',
            'address_en' => 'An address',
            'whatsapp' => '789456123',
            'facebook' => 'www.facebook.com',
            'twitter' => 'www.twitter.com',
            'instagram' => 'www.instagram.com',
            'linkedin' => 'www.linkedin.com',
            'youtube' => 'www.youtube.com',
            'snapchat' => 'www.snapchat.com',
            'tiktok' => 'www.tiktok.com',
            'telegram' => 'www.telegram.com',
        ], $overrides));
    }

    /**
     * @return array<string, string>
     */
    private function adminPayload(): array
    {
        return [
            'email' => 'test@brws.com',
            'phone' => '1234567890',
            'address_ar' => 'عنوان',
            'address_en' => 'An address',
            'whatsapp' => '789456123',
            'facebook' => 'https://www.facebook.com',
            'twitter' => 'https://www.twitter.com',
            'instagram' => 'https://www.instagram.com',
            'linkedin' => 'https://www.linkedin.com',
            'youtube' => 'https://www.youtube.com',
            'snapchat' => 'https://www.snapchat.com',
            'tiktok' => 'https://www.tiktok.com',
            'telegram' => 'https://www.telegram.com',
            'hours' => '',
        ];
    }
}
