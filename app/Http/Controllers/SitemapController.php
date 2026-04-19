<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Field;
use App\Models\Venue;

class SitemapController extends Controller
{
    public function index()
    {
        // Cacheado por 6hs — sitemap no necesita ser tiempo real.
        // Limite de 50k entradas por grupo (Google recomienda máximo 50k URLs por sitemap).
        return cache()->remember('sitemap.xml', now()->addHours(6), function () {
            $venues = Venue::where('is_active', true)
                ->select('id', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(50000)
                ->get();

            $fields = Field::where('is_active', true)
                ->select('id', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(50000)
                ->get();

            $blogPosts = BlogPost::published()
                ->select('slug', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(10000)
                ->get();

            return response()->view('sitemap', compact('venues', 'fields', 'blogPosts'))
                ->header('Content-Type', 'application/xml');
        });
    }
}
