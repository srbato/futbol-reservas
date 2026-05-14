<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Blog público + admin CRUD.
 *  - Público: index sólo posts published, show por slug, search filter
 *  - Admin (super_admin): CRUD, slug auto-generado, status draft/published, published_at automático
 *  - Drafts NO aparecen en público
 *  - Posts programados a futuro NO aparecen
 */
class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_shows_only_published_posts(): void
    {
        $published = BlogPost::factory()->create(['title' => 'Published Post']);
        $draft     = BlogPost::factory()->draft()->create(['title' => 'Draft Post']);
        $scheduled = BlogPost::factory()->scheduledFuture()->create(['title' => 'Future Post']);

        $resp = $this->get(route('blog.index'));

        $resp->assertOk()
            ->assertSee('Published Post')
            ->assertDontSee('Draft Post')
            ->assertDontSee('Future Post');
    }

    public function test_blog_search_filters_by_title_or_excerpt(): void
    {
        BlogPost::factory()->create(['title' => 'Tutorial Padel', 'excerpt' => 'Cómo jugar bien']);
        BlogPost::factory()->create(['title' => 'Otra cosa', 'excerpt' => 'Sin relación']);

        $this->get(route('blog.index') . '?q=padel')
            ->assertOk()
            ->assertSee('Tutorial Padel')
            ->assertDontSee('Otra cosa');
    }

    public function test_blog_show_renders_published_post_by_slug(): void
    {
        $post = BlogPost::factory()->create([
            'title' => 'Cómo elegir cancha de tenis',
            'slug'  => 'como-elegir-cancha-tenis',
        ]);

        $this->get(route('blog.show', 'como-elegir-cancha-tenis'))
            ->assertOk()
            ->assertSee('Cómo elegir cancha de tenis');
    }

    public function test_blog_show_returns_404_for_draft(): void
    {
        $draft = BlogPost::factory()->draft()->create(['slug' => 'draft-post']);

        $this->get(route('blog.show', 'draft-post'))->assertNotFound();
    }

    public function test_blog_show_returns_404_for_unknown_slug(): void
    {
        $this->get(route('blog.show', 'no-existe'))->assertNotFound();
    }

    // ─── Admin CRUD (super_admin only) ───────────────────────────────────

    public function test_super_admin_can_view_admin_index(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        BlogPost::factory()->create();
        BlogPost::factory()->draft()->create();

        $this->actingAs($sa)
            ->get(route('blog-posts.index'))
            ->assertOk();
    }

    public function test_non_super_admin_cannot_access_admin_routes(): void
    {
        $regular = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $resp = $this->actingAs($regular)->get(route('blog-posts.index'));
        $this->assertContains($resp->status(), [302, 403]);
    }

    public function test_super_admin_can_create_blog_post_published(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('blog-posts.store'), [
                'title'  => 'Nuevo Post',
                'body'   => 'Contenido del post...',
                'status' => 'published',
            ])
            ->assertRedirect(route('blog-posts.index'));

        $this->assertDatabaseHas('blog_posts', [
            'title'          => 'Nuevo Post',
            'status'         => 'published',
            'author_user_id' => $sa->id,
        ]);
        $post = BlogPost::where('title', 'Nuevo Post')->first();
        $this->assertNotNull($post->slug);
        $this->assertNotNull($post->published_at);
    }

    public function test_create_as_draft_does_not_set_published_at(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('blog-posts.store'), [
                'title'  => 'Borrador',
                'body'   => 'Contenido',
                'status' => 'draft',
            ])
            ->assertRedirect();

        $post = BlogPost::where('title', 'Borrador')->first();
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
    }

    public function test_validation_requires_title_body_and_status(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('blog-posts.store'), [])
            ->assertSessionHasErrors(['title', 'body', 'status']);
    }

    public function test_status_must_be_draft_or_published(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('blog-posts.store'), [
                'title'  => 'X',
                'body'   => 'Y',
                'status' => 'archived', // inválido
            ])
            ->assertSessionHasErrors(['status']);
    }

    public function test_super_admin_can_update_blog_post(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $post = BlogPost::factory()->draft()->create(['title' => 'Original']);

        $this->actingAs($sa)
            ->put(route('blog-posts.update', $post), [
                'title'  => 'Editado',
                'body'   => 'Cuerpo nuevo',
                'status' => 'published',
            ])
            ->assertRedirect(route('blog-posts.index'));

        $post->refresh();
        $this->assertSame('Editado', $post->title);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_super_admin_can_delete_blog_post(): void
    {
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);
        $post = BlogPost::factory()->create();

        $this->actingAs($sa)
            ->delete(route('blog-posts.destroy', $post))
            ->assertRedirect(route('blog-posts.index'));

        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
    }
}
