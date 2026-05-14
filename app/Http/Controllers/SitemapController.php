<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Field;
use App\Models\Venue;

class SitemapController extends Controller
{
    public function index()
    {
        // Cacheamos SOLO el XML (string), no el Response (que al deserializarlo puede romper).
        // Cache de 6h — sitemap no necesita ser tiempo real.
        // Limite de 50k entradas por grupo (Google recomienda máximo 50k URLs por sitemap).
        $xml = cache()->remember('sitemap.xml', now()->addHours(6), function () {
            // Solo venues/canchas públicamente accesibles: activos y con dueño
            // con suscripción vigente. Si no, venues.show 404ea y Google reporta
            // "No se ha encontrado (404)" en Search Console.
            $venues = Venue::where('is_active', true)
                ->withActiveOwner()
                ->select('id', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(50000)
                ->get();

            $fields = Field::where('is_active', true)
                ->whereHas('venue', fn ($q) => $q->where('is_active', true)->withActiveOwner())
                ->select('id', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(50000)
                ->get();

            $blogPosts = BlogPost::published()
                ->select('slug', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(10000)
                ->get();

            return view('sitemap', compact('venues', 'fields', 'blogPosts'))->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
