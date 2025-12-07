@extends('admin.layouts.app')
@section('title', 'Show Users')
@section('contents')
    <hr />
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail User</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" value="{{ $user->phone_number ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role</label>
                            <div>
                                @if ($user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">User</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Created At</label>
                            <input type="text" class="form-control" 
                                value="{{ $user->created_at->format('d M Y H:i:s') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Updated At</label>
                            <input type="text" class="form-control" 
                                value="{{ $user->updated_at->format('d M Y H:i:s') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('users.index') }}" class="btn btn-primary">Back to List</a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
