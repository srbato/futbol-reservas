<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Field;
use App\Models\Venue;

class SitemapController extends Controller
{
    public function index()
    {
        $venues = Venue::where('is_active', true)->select('id', 'updated_at')->get();
        $fields = Field::where('is_active', true)->select('id', 'updated_at')->get();
        $blogPosts = BlogPost::published()->select('slug', 'updated_at')->get();

        return response()->view('sitemap', compact('venues', 'fields', 'blogPosts'))
            ->header('Content-Type', 'application/xml');
    }
}
