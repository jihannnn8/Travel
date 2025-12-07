@extends('admin.layouts.app')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Destinasi</h1>
        <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Destinasi</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('destinations.update', $destination->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="image_url" class="form-label">Image</label>
                            <div class="mb-3">
                                @php
                                    // Gunakan image_url jika ada, jika tidak gunakan fallback logo
                                    $currentImage = $destination->image_url ?: asset('Asset_Travelo/Logo.png');
                                @endphp
                                <img id="imagePreview" class="img-fluid rounded" 
                                    src="{{ $currentImage }}" 
                                    alt="Current Image" style="max-height: 300px; width: 100%; object-fit: cover;"
                                    onerror="this.onerror=null; this.src='{{ asset('Asset_Travelo/Logo.png') }}';">
                            </div>
                            <input type="file" class="form-control @error('image_url') is-invalid @enderror" 
                                id="image_url" name="image_url" accept="image/*" onchange="previewImage()">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah image. Format: JPG, PNG, GIF (Max: 2MB)</small>
                            @error('image_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title', $destination->title) }}" 
                                placeholder="Contoh: Pantai Lombok" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" 
                                placeholder="Deskripsi singkat paket wisata...">{{ old('description', $destination->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('destination') is-invalid @enderror" 
                                        id="destination" name="destination" value="{{ old('destination', $destination->destination) }}" 
                                        placeholder="Contoh: Lombok, NTB" required>
                                    @error('destination')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                            id="price" name="price" value="{{ old('price', $destination->price) }}" 
                                            placeholder="1000000" min="0" step="0.01" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('duration') is-invalid @enderror" 
                                        id="duration" name="duration" value="{{ old('duration', $destination->duration) }}" 
                                        placeholder="Contoh: 2 Hari 3 Malam" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_date" class="form-label">Departure Date</label>
                                    <input type="date" class="form-control @error('departure_date') is-invalid @enderror" 
                                        id="departure_date" name="departure_date" 
                                        value="{{ old('departure_date', $destination->departure_date ? $destination->departure_date->format('Y-m-d') : '') }}">
                                    @error('departure_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rating" class="form-label">Rating</label>
                                    <input type="number" class="form-control @error('rating') is-invalid @enderror" 
                                        id="rating" name="rating" value="{{ old('rating', $destination->rating) }}" 
                                        min="0" max="5" step="0.1" placeholder="0.0 - 5.0">
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_ratings" class="form-label">Total Ratings</label>
                                    <input type="number" class="form-control @error('total_ratings') is-invalid @enderror" 
                                        id="total_ratings" name="total_ratings" value="{{ old('total_ratings', $destination->total_ratings) }}" 
                                        min="0" placeholder="0">
                                    @error('total_ratings')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rundown" class="form-label">Rundown</label>
                            <div id="rundownContainer">
                                @php
                                    $rundowns = old('rundown', $destination->rundown ?? []);
                                    if (empty($rundowns)) {
                                        $rundowns = [''];
                                    }
                                @endphp
                                @foreach($rundowns as $index => $item)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control rundown-input" 
                                            name="rundown[]" value="{{ $item }}" 
                                            placeholder="Contoh: Hari {{ $index + 1 }}: Kedatangan di Lombok">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger removeRundown" 
                                                style="display: {{ count($rundowns) > 1 ? 'block' : 'none' }};">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="addRundown">
                                <i class="fas fa-plus"></i> Tambah Rundown
                            </button>
                            <small class="form-text text-muted">Tambahkan kegiatan per hari</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage() {
        const image = document.querySelector('input[name="image_url"]');
        const imagePreview = document.getElementById('imagePreview');

        if (image.files && image.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(image.files[0]);
        }
    }

    // Add rundown field
    document.getElementById('addRundown').addEventListener('click', function() {
        const container = document.getElementById('rundownContainer');
        const newInput = document.createElement('div');
        newInput.className = 'input-group mb-2';
        const count = container.querySelectorAll('.rundown-input').length + 1;
        newInput.innerHTML = `
            <input type="text" class="form-control rundown-input" 
                name="rundown[]" placeholder="Contoh: Hari ${count}: Tour ke Pantai Kuta">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger removeRundown">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newInput);
        updateRemoveButtons();
    });

    // Remove rundown field
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRundown')) {
            e.target.closest('.input-group').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rundowns = document.querySelectorAll('.rundown-input');
        const removeButtons = document.querySelectorAll('.removeRundown');
        if (rundowns.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'block');
        } else {
            removeButtons.forEach(btn => btn.style.display = 'none');
        }
    }
</script>
@endpush
