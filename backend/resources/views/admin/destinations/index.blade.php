@extends('admin.layouts.app')

@section('contents')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Destinations</h1>
        <a href="{{ route('destinations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Destination
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
                            <th>Image</th>
                            <th>Title</th>
                            <th>Destination</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Rating</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($destinations->count() > 0)
                            @foreach ($destinations as $destination)
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        @php
                                            // Gunakan image_url jika ada, jika tidak gunakan fallback logo
                                            $imageUrl = $destination->image_url ? asset('storage/' . $destination->image_url) : asset('Asset_Travelo/Logo.png');
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                            alt="{{ $destination->title }}" 
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                            onerror="this.onerror=null; this.src='{{ asset('Asset_Travelo/Logo.png') }}';">
                                    </td>
                                    <td class="align-middle">
                                        <strong>{{ $destination->title }}</strong>
                                        @if($destination->description)
                                            <br><small class="text-muted">{{ Str::limit($destination->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-info">{{ $destination->destination }}</span>
                                    </td>
                                    <td class="align-middle">
                                        Rp {{ number_format($destination->price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle">{{ $destination->duration }}</td>
                                    <td class="align-middle">
                                        @if($destination->rating > 0)
                                            <i class="fas fa-star text-warning"></i> 
                                            {{ number_format($destination->rating, 1) }} 
                                            <small class="text-muted">({{ $destination->total_ratings }})</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('destinations.show', $destination->id) }}" 
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('destinations.edit', $destination->id) }}" 
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                data-toggle="modal" data-target="#deleteModal{{ $destination->id }}" 
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $destination->id }}" tabindex="-1" 
                                            aria-labelledby="deleteModalLabel{{ $destination->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $destination->id }}">
                                                            Konfirmasi Hapus
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus destinasi 
                                                        <strong>{{ $destination->title }}</strong>?
                                                        <br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <form action="{{ route('destinations.destroy', $destination->id) }}" method="POST" class="d-inline">
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
                                <td class="text-center" colspan="8">
                                    <div class="py-4">
                                        <i class="fas fa-map-marked-alt fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">Belum ada destinasi.</p>
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