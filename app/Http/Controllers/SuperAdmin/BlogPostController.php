<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('author')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('sa.blog-posts.index', compact('posts'));
    }

    public function create()
    {
        return view('sa.blog-posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'status'           => ['required', 'in:draft,published'],
        ]);

        $data['slug'] = BlogPost::generateSlug($data['title']);
        $data['author_user_id'] = auth()->id();

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('blog', 'public');
        }

        unset($data['cover_image']);

        BlogPost::create($data);

        return redirect()->route('blog-posts.index')
            ->with('success', 'Post creado correctamente.');
    }

    public function edit(BlogPost $blogPost)
    {
        return view('sa.blog-posts.edit', ['post' => $blogPost]);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'status'           => ['required', 'in:draft,published'],
        ]);

        if ($data['status'] === 'published' && !$blogPost->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            if ($blogPost->cover_image_path) {
                Storage::disk('public')->delete($blogPost->cover_image_path);
            }
            $data['cover_image_path'] = $request->file('cover_image')->store('blog', 'public');
        }

        unset($data['cover_image']);

        $blogPost->update($data);

        return redirect()->route('blog-posts.index')
            ->with('success', 'Post actualizado correctamente.');
    }

    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->cover_image_path) {
            Storage::disk('public')->delete($blogPost->cover_image_path);
        }

        $blogPost->delete();

        return redirect()->route('blog-posts.index')
            ->with('success', 'Post eliminado.');
    }
}
