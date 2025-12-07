@extends('admin.layouts.app')

@section('title', 'Detail Booking')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Booking</h1>
        <div>
            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Kode Booking:</strong>
                        </div>
                        <div class="col-md-8">
                            <h5 class="text-primary">{{ $booking->kode_booking }}</h5>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>User:</strong>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $booking->user->name ?? '-' }}</p>
                            <small class="text-muted">{{ $booking->user->email ?? '-' }}</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Destination:</strong>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $booking->destination->title ?? '-' }}</p>
                            <small class="text-muted">{{ $booking->destination->destination ?? '-' }}</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tanggal Booking:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tanggal Keberangkatan:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ \Carbon\Carbon::parse($booking->tanggal_keberangkatan)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Waktu Keberangkatan:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $booking->waktu_keberangkatan }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($booking->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($booking->status == 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Metode Pembayaran:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge badge-info">
                                {{ ucfirst(str_replace('-', ' ', $booking->metode_pembayaran)) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Total Harga:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="h5 text-primary">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $booking->created_at->format('d M Y H:i:s') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $booking->updated_at->format('d M Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

            @if($booking->destination)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Destination</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $destination = $booking->destination;
                            $imageUrl = $destination->image_url ?: asset('Asset Travelo/Logo.png');
                        @endphp
                        <div class="text-center mb-3">
                            <img src="{{ $imageUrl }}" 
                                alt="{{ $destination->title }}" 
                                class="img-fluid rounded" 
                                style="max-height: 200px; width: 100%; object-fit: cover;"
                                onerror="this.onerror=null; this.src='{{ asset('Asset Travelo/Logo.png') }}';">
                        </div>
                        <p class="mb-2">
                            <strong>Title:</strong><br>
                            {{ $destination->title }}
                        </p>
                        <p class="mb-2">
                            <strong>Price:</strong><br>
                            <span class="text-success">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Duration:</strong><br>
                            {{ $destination->duration }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

