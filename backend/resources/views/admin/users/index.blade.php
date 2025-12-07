@extends('admin.layouts.app')
@section('title', 'List Users')
@section('contents')

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add Users</a>
    </div>

    <hr />
    
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($users->count() > 0)
                    @foreach ($users as $user)
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle">{{ $user->name }}</td>
                            <td class="align-middle">{{ $user->email }}</td>
                            <td class="align-middle">{{ $user->phone_number ?? '-' }}</td>
                            <td class="align-middle">
                                @if ($user->role == 'admin')
                                    <!-- Badge merah untuk admin -->
                                    <span class="badge bg-danger">
                                        Admin</span>
                                @else
                                    <!-- Badge biru untuk user -->
                                    <span class="badge bg-primary">
                                        User</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group" aria-label="Aksi">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">Detail</a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $user->id }}">
                                        Hapus
                                    </button>

                                    <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">
                                                        Konfirmasi Hapus</h5>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah kamu yakin ingin menghapus user
                                                    <strong>{{ $user->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger">Ya, Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <!-- Pesan jika tidak ada data users -->
                    <tr>
                        <td class="text-center" colspan="7">Tidak ada data user.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
