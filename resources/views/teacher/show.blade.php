@extends('layouts.admin')

@section('title','Teacher Profile')

@section('heading','Teacher Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to List
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-primary btn-sm">
            <i class="fa fa-pen me-1"></i> Edit
        </a>
        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
              onsubmit="return confirm('Delete {{ $teacher->full_name }}? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">
                <i class="fa fa-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <img src="{{ $teacher->photo_url ?? 'https://ui-avatars.com/api/?background=1b263b&color=fff&size=160&name='.urlencode($teacher->full_name) }}"
                     class="rounded-circle border mb-3" width="140" height="140" alt="{{ $teacher->full_name }}">
                <h4 class="mb-0">{{ $teacher->full_name }}</h4>
                <p class="text-muted mb-2">{{ $teacher->subject }} Teacher</p>
                @if($teacher->status)
                    <span class="badge bg-success badge-status">Active</span>
                @else
                    <span class="badge bg-danger badge-status">Inactive</span>
                @endif
                <hr>
                <p class="mb-1"><i class="fa fa-id-badge me-2 text-muted"></i>{{ $teacher->teacher_id }}</p>
                <p class="mb-1"><i class="fa fa-envelope me-2 text-muted"></i>{{ $teacher->email }}</p>
                <p class="mb-0"><i class="fa fa-phone me-2 text-muted"></i>{{ $teacher->phone }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">

        <div class="card shadow-sm mb-3">
            <div class="card-header"><i class="fa fa-id-card me-1"></i> Personal Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Gender</small>
                        {{ $teacher->gender }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Date of Birth</small>
                        {{ optional($teacher->dob)->format('d M, Y') ?? '—' }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">CNIC</small>
                        {{ $teacher->cnic }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Address</small>
                        {{ $teacher->address ?: '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header"><i class="fa fa-briefcase me-1"></i> Employment Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Qualification</small>
                        {{ $teacher->qualification }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Experience</small>
                        {{ $teacher->experience }} years
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Joining Date</small>
                        {{ optional($teacher->joining_date)->format('d M, Y') ?? '—' }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Salary</small>
                        Rs {{ number_format($teacher->salary, 2) }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Class</small>
                        {{ $teacher->class_id ?: '—' }}
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Section</small>
                        {{ $teacher->section_id ?: '—' }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
