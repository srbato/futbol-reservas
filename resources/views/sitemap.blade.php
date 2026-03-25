<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  {{-- Páginas estáticas --}}
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ route('venues.index') }}</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>{{ url('/como-funciona') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>{{ url('/nosotros') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>

  {{-- Complejos --}}
  @foreach($venues as $venue)
  <url>
    <loc>{{ route('venues.show', $venue->id) }}</loc>
    <lastmod>{{ $venue->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  {{-- Canchas --}}
  @foreach($fields as $field)
  <url>
    <loc>{{ route('fields.show', $field->id) }}</loc>
    <lastmod>{{ $field->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach

</urlset>
