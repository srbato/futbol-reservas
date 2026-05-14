<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Páginas públicas + endpoints utilitarios:
 *  - Páginas estáticas (home, como-funciona, nosotros, planes, faq, etc.)
 *  - Sitemap XML cacheado
 *  - Feedback form (rate limited)
 *  - Contact form (rate limited + envío async)
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_home_page_renders(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_static_pages_render(): void
    {
        $this->get('/como-funciona')->assertOk();
        $this->get('/nosotros')->assertOk();
        $this->get('/por-que-tucancha')->assertOk();
        $this->get('/preguntas-frecuentes')->assertOk();
    }

    public function test_planes_page_renders(): void
    {
        $this->get('/planes')->assertOk();
        $this->get('/para-complejos')->assertOk();
    }

    public function test_venues_index_renders(): void
    {
        $this->get(route('venues.index'))->assertOk();
    }

    // ─── Sitemap ─────────────────────────────────────────────────────────

    public function test_sitemap_returns_xml_with_correct_content_type(): void
    {
        // Limpiar cache para que regenere
        cache()->forget('sitemap.xml');

        $resp = $this->get(route('sitemap'));

        $resp->assertOk();
        $this->assertStringContainsString('application/xml', $resp->headers->get('Content-Type'));
        $body = $resp->getContent();
        $this->assertStringContainsString('<?xml', $body);
        $this->assertStringContainsString('<urlset', $body);
        $this->assertStringContainsString('</urlset>', $body);
    }

    public function test_sitemap_includes_static_routes_and_published_blog_posts(): void
    {
        cache()->forget('sitemap.xml');

        $venue = $this->makeVenue();
        $field = $this->makeField($venue);
        $blog  = BlogPost::factory()->create(['slug' => 'test-blog-post-sitemap']);
        BlogPost::factory()->draft()->create(['slug' => 'draft-not-in-sitemap']);

        $resp = $this->get(route('sitemap'));
        $body = $resp->getContent();

        $this->assertStringContainsString('/venues', $body);
        $this->assertStringContainsString('/como-funciona', $body);
        $this->assertStringContainsString('test-blog-post-sitemap', $body);
        // Drafts NO deben estar en el sitemap
        $this->assertStringNotContainsString('draft-not-in-sitemap', $body);
    }

    public function test_sitemap_is_cached(): void
    {
        cache()->forget('sitemap.xml');
        $this->get(route('sitemap'));

        // Después de la primera llamada, hay un valor en cache
        $this->assertNotNull(cache()->get('sitemap.xml'));
    }

    // ─── Feedback ────────────────────────────────────────────────────────

    public function test_feedback_form_accepts_valid_submission(): void
    {
        Mail::fake();

        $resp = $this->post('/feedback', [
            'feedback_message' => 'Me gusta mucho la plataforma, muy buena la app',
            'feedback_email'   => 'test@test.com',
            'form_loaded_at'   => now()->subMinute()->valueOf(),
        ]);

        $this->assertContains($resp->status(), [200, 201, 302]);
        Mail::assertQueued(\App\Mail\FeedbackMail::class);
    }

    public function test_feedback_honeypot_silently_drops_bot_submission(): void
    {
        Mail::fake();

        $this->post('/feedback', [
            'feedback_message' => 'Mensaje de prueba largo suficiente',
            'website_url'      => 'http://spam.example',
            'form_loaded_at'   => now()->subMinute()->valueOf(),
        ]);

        Mail::assertNothingQueued();
    }

    public function test_feedback_rejects_too_fast_submission(): void
    {
        Mail::fake();

        // form_loaded_at hace 1 segundo → humano imposible
        $this->post('/feedback', [
            'feedback_message' => 'Mensaje de prueba largo suficiente',
            'form_loaded_at'   => now()->subSecond()->valueOf(),
        ]);

        Mail::assertNothingQueued();
    }

    public function test_feedback_rejects_non_latin_spam(): void
    {
        Mail::fake();

        $this->post('/feedback', [
            'feedback_message' => 'Привет всем закажите продвижение сайта дёшево сейчас',
            'form_loaded_at'   => now()->subMinute()->valueOf(),
        ]);

        Mail::assertNothingQueued();
    }

    public function test_feedback_rejects_message_with_multiple_links(): void
    {
        Mail::fake();

        $this->post('/feedback', [
            'feedback_message' => 'Check http://a.com and http://b.com and http://c.com now',
            'form_loaded_at'   => now()->subMinute()->valueOf(),
        ]);

        Mail::assertNothingQueued();
    }

    // ─── Contact ─────────────────────────────────────────────────────────

    public function test_contact_form_validates_required_fields(): void
    {
        $this->post('/contact', [])
            ->assertSessionHasErrors(['name', 'email', 'reason', 'message']);
    }

    public function test_contact_form_validates_email_format(): void
    {
        $this->post('/contact', [
            'name'    => 'Test',
            'email'   => 'not-an-email',
            'reason'  => 'Consulta',
            'message' => 'Hola',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_contact_form_accepts_valid_submission(): void
    {
        Queue::fake();
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'Santiago',
            'email'   => 'srbattini@gmail.com',
            'reason'  => 'Consulta general',
            'message' => 'Hola, quería preguntar algo.',
        ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');
    }
}
