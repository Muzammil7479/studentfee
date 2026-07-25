@extends('layouts.admin')

@section('title','Add Teacher')

@section('heading','Add New Teacher')

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

        <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    <img id="photoPreview" src="https://ui-avatars.com/api/?background=1b263b&color=fff&size=128&name=New+Teacher"
                         class="rounded-circle border mb-2" width="110" height="110" alt="Photo preview">
                    <div>
                        <label class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-camera me-1"></i> Upload Photo
                            <input type="file" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                        </label>
                    </div>
                </div>
                <div class="col-md-10">
                    <h5 class="mb-1"><i class="fa fa-id-card me-1 text-primary"></i> Personal Information</h5>
                    <p class="text-muted mb-0">Basic details used to identify the teacher.</p>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                        <option value="Male" {{ old('gender')=='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender')=='Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender')=='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">CNIC <span class="text-danger">*</span></label>
                    <input type="text" name="cnic" value="{{ old('cnic') }}" placeholder="xxxxx-xxxxxxx-x" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                </div>

            </div>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fa fa-briefcase me-1 text-primary"></i> Employment Details</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Qualification <span class="text-danger">*</span></label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Experience (Years) <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="experience" value="{{ old('experience') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (Rs) <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="0.01" name="salary" value="{{ old('salary') }}" class="form-control" required>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Class</label>
                    <input type="text" name="class_id" value="{{ old('class_id') }}" class="form-control" placeholder="e.g. 9">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Section</label>
                    <input type="text" name="section_id" value="{{ old('section_id') }}" class="form-control" placeholder="e.g. A">
                </div>

            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success">
                    <i class="fa fa-floppy-disk me-1"></i> Save Teacher
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
