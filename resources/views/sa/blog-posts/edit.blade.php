@extends('layouts.admin')

@section('title', 'Editar Post')
@section('page-title', 'Editar Post')

@section('content')

<form method="POST" action="{{ route('blog-posts.update', $post) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-sm text-slate-700 mb-4">Contenido</h3>

    <div class="grid gap-5">
      {{-- Titulo --}}
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="title">Titulo *</label>
        <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors">
        @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        <p class="text-xs text-slate-400 mt-1">Slug: /blog/{{ $post->slug }}</p>
      </div>

      {{-- Excerpt --}}
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="excerpt">Extracto</label>
        <textarea name="excerpt" id="excerpt" rows="2"
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors resize-vertical">{{ old('excerpt', $post->excerpt) }}</textarea>
        @error('excerpt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Body --}}
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="body">Contenido *</label>
        <textarea name="body" id="body" rows="16" required
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors resize-vertical font-mono">{{ old('body', $post->body) }}</textarea>
        <p class="text-xs text-slate-400 mt-1">Podes usar HTML para formato: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;a&gt;, &lt;blockquote&gt;</p>
        @error('body') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Cover image --}}
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Imagen de portada</label>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center cursor-pointer hover:border-slate-300 transition-colors"
             onclick="document.getElementById('cover_image').click()">
          <input type="file" name="cover_image" id="cover_image" accept="image/*" class="hidden"
                 onchange="previewCover(this)">
          @if($post->cover_image_path)
            <img id="coverPreview" src="{{ Storage::url($post->cover_image_path) }}"
                 class="mx-auto h-36 rounded-xl object-cover shadow-sm mb-3">
            <p class="text-sm text-slate-400" id="coverText">Clic para cambiar imagen</p>
          @else
            <img id="coverPreview" class="mx-auto h-36 rounded-xl object-cover shadow-sm mb-3 hidden">
            <p class="text-sm text-slate-400" id="coverText">Arrastra o hace clic para subir</p>
          @endif
        </div>
        @error('cover_image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-sm text-slate-700 mb-4">SEO</h3>

    <div class="grid gap-5">
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="meta_title">Meta titulo</label>
        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors"
               placeholder="Titulo para Google (50-60 caracteres)">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="meta_description">Meta descripcion</label>
        <textarea name="meta_description" id="meta_description" rows="2"
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors resize-vertical">{{ old('meta_description', $post->meta_description) }}</textarea>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="font-bold text-sm text-slate-700 mb-4">Publicacion</h3>

    <div>
      <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" for="status">Estado</label>
      <select name="status" id="status"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:border-slate-900 focus:ring-0 outline-none transition-colors">
        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Publicado</option>
      </select>
      @if($post->published_at)
        <p class="text-xs text-slate-400 mt-1">Publicado el {{ $post->published_at->format('d/m/Y H:i') }}</p>
      @endif
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit"
            class="px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition-colors">
      Guardar cambios
    </button>
    <a href="{{ route('blog-posts.index') }}" class="text-sm text-slate-400 hover:text-slate-600 font-semibold">Cancelar</a>
  </div>
</form>

<script>
function previewCover(input) {
  const preview = document.getElementById('coverPreview');
  const text = document.getElementById('coverText');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      text.textContent = input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

@endsection
