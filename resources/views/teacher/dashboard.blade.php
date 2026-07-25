@extends('layouts.admin')

@section('title','Teacher Dashboard')

@section('heading','Teacher Dashboard')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">

    <div class="col-md-3">
        <div class="card-box blue">
            <h5><i class="fa fa-chalkboard-teacher me-1"></i> Total Teachers</h5>
            <h2>{{ $totalTeachers }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box green">
            <h5><i class="fa fa-user-check me-1"></i> Active Teachers</h5>
            <h2>{{ $activeTeachers }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box orange">
            <h5><i class="fa fa-user-slash me-1"></i> Inactive Teachers</h5>
            <h2>{{ $inactiveTeachers }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box red">
            <h5><i class="fa fa-sack-dollar me-1"></i> Monthly Salary</h5>
            <h2>Rs {{ number_format($totalSalary) }}</h2>
        </div>
    </div>

</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fa fa-gear me-1"></i> Teacher Management</span>
    </div>
    <div class="card-body d-flex gap-2 flex-wrap">
        <a href="{{ route('teachers.create') }}" class="btn btn-success">
            <i class="fa fa-plus me-1"></i> Add Teacher
        </a>
        <a href="{{ route('teachers.index') }}" class="btn btn-primary">
            <i class="fa fa-list me-1"></i> Teacher List
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="fa fa-clock-rotate-left me-1"></i> Recently Added Teachers
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Teacher ID</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th class="pe-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTeachers as $teacher)
                    <tr>
                        <td class="ps-3">{{ $teacher->teacher_id }}</td>
                        <td>{{ $teacher->full_name }}</td>
                        <td>{{ $teacher->subject }}</td>
                        <td>
                            @if($teacher->status)
                                <span class="badge bg-success badge-status">Active</span>
                            @else
                                <span class="badge bg-danger badge-status">Inactive</span>
                            @endif
                        </td>
                        <td class="pe-3 text-end">
                            <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No teachers added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
