@extends('admin.layouts.app')

@section('title', 'Tambah Booking')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Booking</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Booking</h6>
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

            <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" 
                                id="user_id" name="user_id" required>
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination_id" class="form-label">Destination <span class="text-danger">*</span></label>
                            <select class="form-control @error('destination_id') is-invalid @enderror" 
                                id="destination_id" name="destination_id" required>
                                <option value="">Pilih Destination</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>
                                        {{ $destination->title }} - Rp {{ number_format($destination->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_booking" class="form-label">Tanggal Booking <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_booking') is-invalid @enderror" 
                                id="tanggal_booking" name="tanggal_booking" 
                                value="{{ old('tanggal_booking', date('Y-m-d')) }}" required>
                            @error('tanggal_booking')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_keberangkatan" class="form-label">Tanggal Keberangkatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_keberangkatan') is-invalid @enderror" 
                                id="tanggal_keberangkatan" name="tanggal_keberangkatan" 
                                value="{{ old('tanggal_keberangkatan') }}" required>
                            @error('tanggal_keberangkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="waktu_keberangkatan" class="form-label">Waktu Keberangkatan <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('waktu_keberangkatan') is-invalid @enderror" 
                                id="waktu_keberangkatan" name="waktu_keberangkatan" 
                                value="{{ old('waktu_keberangkatan', '08:00') }}" required>
                            @error('waktu_keberangkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control @error('metode_pembayaran') is-invalid @enderror" 
                                id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="e-wallet" {{ old('metode_pembayaran') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                            </select>
                            @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_harga" class="form-label">Total Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('total_harga') is-invalid @enderror" 
                                id="total_harga" name="total_harga" 
                                value="{{ old('total_harga') }}" 
                                placeholder="0" min="0" step="0.01" required>
                            @error('total_harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewBukti() {
            const file = document.getElementById('bukti_pembayaran').files[0];
            const preview = document.getElementById('buktiPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        // Auto-fill total harga berdasarkan destination
        document.getElementById('destination_id').addEventListener('change', function() {
            const destinationId = this.value;
            if (destinationId) {
                // Fetch destination price via AJAX or use data attribute
                // For now, we'll let admin fill manually
            }
        });
    </script>
@endsection

