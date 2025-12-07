@extends('admin.layouts.app')
@section('title', 'Profile')

@section('contents')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
                </div>
                <div class="card-body">
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                id="phone_number" name="phone_number" 
                                value="{{ old('phone_number', $user->phone_number) }}" 
                                placeholder="081234567890">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="form-text text-muted">Role tidak dapat diubah.</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3 font-weight-bold text-primary">Ubah Password</h6>
                        <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>

                        <div class="form-group">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password" 
                                placeholder="Masukkan password lama">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                id="new_password" name="new_password" 
                                placeholder="Masukkan password baru (minimal 8 karakter)">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" 
                                id="new_password_confirmation" name="new_password_confirmation" 
                                placeholder="Konfirmasi password baru">
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Profile Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img class="img-profile rounded-circle" 
                            src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1976D2&color=fff&size=128' }}"
                            alt="Profile" style="width: 128px; height: 128px; object-fit: cover;">
                    </div>
                    <h5 class="font-weight-bold">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge badge-{{ $user->role == 'admin' ? 'primary' : 'secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <hr>
                    <div class="text-left">
                        <p class="mb-2">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                            <strong>Phone:</strong> {{ $user->phone_number ?? '-' }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>
                            <strong>Bergabung:</strong> {{ $user->created_at->format('d M Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            <strong>Terakhir Update:</strong> {{ $user->updated_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

