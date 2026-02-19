@extends('layouts.app')
@section('title', $post->exists ? 'Edit Berita' : 'Tambah Berita')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $post->exists ? 'Edit Berita' : 'Tambah Berita' }}</h4>
            <div class="text-muted">Kelola konten berita untuk landing page.</div>
        </div>
        <div>
            <a class="btn btn-outline-light btn-sm" href="{{ route('admin.news-posts.index') }}">Kembali</a>
        </div>
    </div>

    <div class="card trezo-card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"
                action="{{ $post->exists ? route('admin.news-posts.update', $post) : route('admin.news-posts.store') }}">
                @csrf
                @if ($post->exists)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="news_category_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(old('news_category_id', $post->news_category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('news_category_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" required
                            value="{{ old('title', $post->title) }}">
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Ringkasan (Opsional)</label>
                    <textarea name="excerpt" class="form-control" rows="3"
                        placeholder="Ringkasan singkat untuk landing card...">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <label class="form-label">Konten Berita</label>
                    <input type="hidden" name="content" id="contentInput" value="{{ old('content', $post->content) }}">
                    <div id="editor" style="min-height: 220px;"></div>
                    @error('content')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        @error('thumbnail')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @if ($post->thumbnail_url)
                            <div class="mt-2">
                                <img src="{{ $post->thumbnail_url }}" alt="Thumbnail" style="max-height: 160px;">
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_thumbnail" value="1"
                                    id="removeThumbnail">
                                <label class="form-check-label" for="removeThumbnail">Hapus thumbnail</label>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Publish</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                id="isPublished"
                                @checked(old('is_published', $post->exists ? $post->is_published : true))>
                            <label class="form-check-label" for="isPublished">Tampilkan di landing</label>
                        </div>

                        <label class="form-label">Tanggal Publish (Opsional)</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                            value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\\TH:i')) }}">
                        @error('published_at')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary">{{ $post->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    <a class="btn btn-outline-light" href="{{ route('admin.news-posts.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/css/quill.snow.css') }}">
@endpush

@push('scripts')
    <script src="{{ url('assets/js/quill.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editor = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            const initialContent = @json(old('content', $post->content));
            if (initialContent) {
                editor.root.innerHTML = initialContent;
            }

            const form = editor.root.closest('form');
            form.addEventListener('submit', function () {
                document.getElementById('contentInput').value = editor.root.innerHTML;
            });
        });
    </script>
@endpush
