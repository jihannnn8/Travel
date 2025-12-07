@extends('admin.layouts.app')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Destinasi</h1>
        <div>
            <a href="{{ route('destinations.edit', $destination->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('destinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Destinasi</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Title:</strong>
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $destination->title }}</h5>
                        </div>
                    </div>

                    @if($destination->description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $destination->description }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Destination:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge badge-info">{{ $destination->destination }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Price:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="h5 text-primary">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Duration:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $destination->duration }}
                        </div>
                    </div>

                    @if($destination->departure_date)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Departure Date:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $destination->departure_date->format('d M Y') }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Rating:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($destination->rating > 0)
                                <i class="fas fa-star text-warning"></i> 
                                <strong>{{ number_format($destination->rating, 1) }}</strong>
                                <span class="text-muted">({{ $destination->total_ratings }} ratings)</span>
                            @else
                                <span class="text-muted">Belum ada rating</span>
                            @endif
                        </div>
                    </div>

                    @if($destination->rundown && count($destination->rundown) > 0)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Rundown:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="border rounded p-3 bg-light">
                                <ul class="mb-0 pl-3">
                                    @foreach($destination->rundown as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $destination->created_at->format('d M Y H:i:s') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $destination->updated_at->format('d M Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Image</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        // Gunakan image_url jika ada, jika tidak gunakan fallback logo
                        $imageUrl = $destination->image_url ?: asset('Asset_Travelo/Logo.png');
                    @endphp
                    <img src="{{ $imageUrl }}" 
                        alt="{{ $destination->title }}" 
                        class="img-fluid rounded" 
                        style="max-height: 400px; width: 100%; object-fit: cover;"
                        onerror="this.onerror=null; this.src='{{ asset('Asset_Travelo/Logo.png') }}';">
                </div>
            </div>

            @if($destination->bookings->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistik Booking</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Total Booking:</strong> {{ $destination->bookings->count() }}
                        </p>
                        <p class="mb-0">
                            <strong>Total Revenue:</strong> 
                            <span class="text-success">
                                Rp {{ number_format($destination->bookings->sum('total_harga'), 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
