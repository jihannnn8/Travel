@extends('admin.layouts.app')

@section('title', 'Bookings')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bookings</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Booking
        </a>
    </div>

    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Booking</th>
                            <th>User</th>
                            <th>Destination</th>
                            <th>Tanggal Booking</th>
                            <th>Tanggal Keberangkatan</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($bookings->count() > 0)
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong class="text-primary">{{ $booking->kode_booking }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        {{ $booking->user->name ?? '-' }}
                                        <br><small class="text-muted">{{ $booking->user->email ?? '-' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        {{ $booking->destination->title ?? '-' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}
                                    </td>
                                    <td class="align-middle">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keberangkatan)->format('d M Y') }}
                                        <br><small class="text-muted">{{ $booking->waktu_keberangkatan }}</small>
                                    </td>
                                    <td class="align-middle">
                                        Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle">
                                        @if($booking->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($booking->status == 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('bookings.show', $booking->id) }}" 
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bookings.edit', $booking->id) }}" 
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                data-toggle="modal" data-target="#deleteModal{{ $booking->id }}" 
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $booking->id }}" tabindex="-1" 
                                            aria-labelledby="deleteModalLabel{{ $booking->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $booking->id }}">
                                                            Konfirmasi Hapus
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus booking 
                                                        <strong>{{ $booking->kode_booking }}</strong>?
                                                        <br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="9">
                                    <div class="py-4">
                                        <i class="fas fa-calendar-check fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">Belum ada booking.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

