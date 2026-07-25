@extends('layouts.admin')

@section('title', 'User Management')
@section('heading', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="js-live-search d-flex" method="GET" action="{{ route('admin.users.index') }}">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ $search }}">
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary ms-2 text-nowrap">
        <i class="fa-solid fa-user-plus me-1"></i> Add User
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge {{ $u->isAdmin() ? 'bg-danger' : 'bg-secondary' }}">
                                {{ $u->roleLabel() }}
                            </span>
                        </td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $users->links() }}
    </div>
</div>
@endsection
