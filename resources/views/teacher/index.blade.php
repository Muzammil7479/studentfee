@extends('layouts.admin')

@section('title','Teachers')

@section('heading','Teacher Management')

@section('content')

<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0">Teachers List <small class="text-muted">({{ $teachers->total() }} found)</small></h4>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i> Add Teacher
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" action="{{ route('teachers.index') }}" class="row g-2 mb-3 js-live-search">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                           placeholder="Search by full name, teacher ID, email, subject, phone, CNIC, class, section or address">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary">
                    <i class="fa fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Teacher ID</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $teacher->photo_url ?? 'https://ui-avatars.com/api/?background=1b263b&color=fff&size=64&name='.urlencode($teacher->full_name) }}"
                                         class="rounded-circle" width="36" height="36" alt="{{ $teacher->full_name }}">
                                    <span>{{ $teacher->full_name }}</span>
                                </div>
                            </td>
                            <td>{{ $teacher->teacher_id }}</td>
                            <td>{{ $teacher->gender }}</td>
                            <td>{{ $teacher->phone }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->subject }}</td>
                            <td>Rs {{ number_format($teacher->salary) }}</td>
                            <td>
                                @if($teacher->status)
                                    <span class="badge bg-success badge-status">Active</span>
                                @else
                                    <span class="badge bg-danger badge-status">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
                                          onsubmit="return confirm('Delete {{ $teacher->full_name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No teachers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $teachers->links() }}
        </div>

    </div>
</div>

@endsection
