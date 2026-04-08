@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts')

@section('content')

<div class="flex items-center justify-between mb-6">
  <p class="text-sm text-slate-500">{{ $posts->total() }} {{ $posts->total() === 1 ? 'post' : 'posts' }}</p>
  <a href="{{ route('blog-posts.create') }}"
     class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
    Nuevo post
  </a>
</div>

@if($posts->isEmpty())
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
    <svg class="mx-auto mb-4" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
    <p class="font-bold text-slate-700 mb-1">Sin posts todavia</p>
    <p class="text-sm text-slate-400">Crea tu primer post para empezar a generar contenido SEO.</p>
  </div>
@else
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-slate-100 text-left">
          <th class="px-5 py-3 font-bold text-xs text-slate-400 uppercase tracking-wider">Titulo</th>
          <th class="px-5 py-3 font-bold text-xs text-slate-400 uppercase tracking-wider">Status</th>
          <th class="px-5 py-3 font-bold text-xs text-slate-400 uppercase tracking-wider">Fecha</th>
          <th class="px-5 py-3 font-bold text-xs text-slate-400 uppercase tracking-wider text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($posts as $post)
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
          <td class="px-5 py-4">
            <div class="font-bold text-slate-800">{{ $post->title }}</div>
            <div class="text-xs text-slate-400 mt-0.5">/blog/{{ $post->slug }}</div>
          </td>
          <td class="px-5 py-4">
            @if($post->status === 'published')
              <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">Publicado</span>
            @else
              <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full">Borrador</span>
            @endif
          </td>
          <td class="px-5 py-4 text-slate-400">
            {{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}
          </td>
          <td class="px-5 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              @if($post->status === 'published')
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                   class="text-xs text-slate-400 hover:text-slate-700 font-semibold">Ver</a>
              @endif
              <a href="{{ route('blog-posts.edit', $post) }}"
                 class="text-xs text-blue-600 hover:text-blue-800 font-semibold">Editar</a>
              <form method="POST" action="{{ route('blog-posts.destroy', $post) }}"
                    onsubmit="return confirm('Eliminar este post?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $posts->links() }}
  </div>
@endif

@endsection
