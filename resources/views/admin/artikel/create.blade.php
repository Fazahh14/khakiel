@extends('layouts.admin')

@section('title', 'Tambah Artikel')

@section('content')
@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<style>
    .form-wrapper {
        background-color: #E5CBB7;
        border-radius: 20px;
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .form-wrapper h2 {
        font-weight: bold;
        margin-bottom: 30px;
        text-align: center;
    }

    .btn-simpan {
        background-color: #198754;
        color: #fff;
        font-weight: 600;
    }

    .btn-simpan:hover {
        background-color: #157347;
    }

    .preview-img {
        width: 140px;
        height: 100px;
        margin-top: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        object-fit: cover;
        display: none;
    }

    /* Tambahan untuk memperbesar area CKEditor (sekitar 2 baris lebih tinggi) */
    .ck-editor__editable_inline {
        min-height: 250px !important;
    }
</style>

<div class="form-wrapper">
    <h2>Tambah Artikel</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.artikel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Judul --}}
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Artikel</label>
            <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required class="form-control bg-light">
            @error('judul') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Konten --}}
        <div class="mb-3">
            <label for="konten" class="form-label">Konten</label>
            <textarea name="konten" id="konten" class="form-control bg-light">{{ old('konten') }}</textarea>
            @error('konten') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Gambar --}}
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Utama</label>
            <input type="file" name="gambar" id="gambar" class="form-control" onchange="previewGambar(event)">
            @error('gambar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            <img id="preview" class="preview-img" alt="Preview Gambar">
        </div>

        {{-- Tombol --}}
        <div class="text-center mt-4 d-flex justify-content-center gap-2">
            <a href="{{ route('admin.artikel.index') }}" class="btn btn-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-simpan px-4">Simpan Artikel</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('upload', file);

                fetch("{{ route('admin.artikel.upload.gambar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    if (result.url) {
                        resolve({ default: result.url });
                    } else {
                        reject(result.message || 'Upload gagal');
                    }
                })
                .catch(error => {
                    reject('Upload gagal: ' + error.message);
                });
            }));
        }

        abort() {}
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    ClassicEditor
        .create(document.querySelector('#konten'), {
            extraPlugins: [MyCustomUploadAdapterPlugin],
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'insertTable', 'blockQuote', 'undo', 'redo', '|',
                    'imageUpload', 'mediaEmbed', 'htmlEmbed'
                ]
            },
            htmlEmbed: {
                showPreviews: true
            }
        })
        .catch(error => {
            console.error(error);
        });

    function previewGambar(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const preview = document.getElementById('preview');
            preview.src = reader.result;
            preview.style.display = 'block';
        }
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
