@extends('layouts.admin')

@section('title','Edit Teacher')

@section('heading','Edit Teacher')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to List
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fa fa-triangle-exclamation me-1"></i> Please fix the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    <img id="photoPreview"
                         src="{{ $teacher->photo_url ?? 'https://ui-avatars.com/api/?background=1b263b&color=fff&size=128&name='.urlencode($teacher->full_name) }}"
                         class="rounded-circle border mb-2" width="110" height="110" alt="{{ $teacher->full_name }}">
                    <div>
                        <label class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-camera me-1"></i> Change Photo
                            <input type="file" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                        </label>
                    </div>
                </div>
                <div class="col-md-10">
                    <h5 class="mb-1"><i class="fa fa-id-card me-1 text-primary"></i> Personal Information</h5>
                    <p class="text-muted mb-2">Teacher ID: <strong>{{ $teacher->teacher_id }}</strong></p>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ $teacher->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusSwitch">Active</label>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" {{ old('gender', $teacher->gender)=='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $teacher->gender)=='Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $teacher->gender)=='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', optional($teacher->dob)->format('Y-m-d')) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">CNIC <span class="text-danger">*</span></label>
                    <input type="text" name="cnic" value="{{ old('cnic', $teacher->cnic) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $teacher->address) }}" class="form-control">
                </div>

            </div>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fa fa-briefcase me-1 text-primary"></i> Employment Details</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Experience (Years) <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="experience" value="{{ old('experience', $teacher->experience) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $teacher->subject) }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date"
                           value="{{ old('joining_date', optional($teacher->joining_date)->format('Y-m-d')) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (Rs) <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="0.01" name="salary" value="{{ old('salary', $teacher->salary) }}" class="form-control" required>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Class</label>
                    <input type="text" name="class_id" value="{{ old('class_id', $teacher->class_id) }}" class="form-control">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Section</label>
                    <input type="text" name="section_id" value="{{ old('section_id', $teacher->section_id) }}" class="form-control">
                </div>

            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success">
                    <i class="fa fa-floppy-disk me-1"></i> Update Teacher
                </button>
                <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
