@extends('layouts.admin')

@section('title','SchoolM Admin')
@section('heading','Admin Student Admission')

@section('content')
<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold"><i class="fa fa-user-plus me-1"></i> Admit New Student</div>
    <div class="card-body">
        <form action="{{ route('admin.students.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-3"><label class="form-label">First Name</label><input name="first_name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Middle Name</label><input name="middle_name" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Last Name</label><input name="last_name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select" required><option>Male</option><option>Female</option></select></div>

            <div class="col-md-3"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Student Contact</label><input name="contact_no" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Student Address</label><input name="address" class="form-control"></div>

            <div class="col-md-3">
                <label class="form-label">Class</label>
                <input list="classOptionsList" name="class_name" class="form-control" value="Grade 10" required>
                <datalist id="classOptionsList">
                    @foreach($classOptions as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <small class="text-muted">Admin can select/name class from Playgroup to 10th. Existing class fee structures will auto-assign on admission.</small>
            </div>
            <div class="col-md-2"><label class="form-label">Academic Year</label><input name="academic_year" class="form-control" value="{{ date('Y') }}-{{ date('Y')+1 }}"></div>
            <div class="col-md-2"><label class="form-label">Section</label><input name="section_name" class="form-control" value="Section A"></div>
            <div class="col-md-2"><label class="form-label">Term</label><select name="term_id" class="form-select">@foreach($terms as $term)<option value="{{ $term->TermID }}">{{ $term->TermName }}</option>@endforeach</select><small class="text-muted">Fee ledgers are auto-assigned from all class fee structures.</small></div>
            <div class="col-md-3"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"><small class="text-muted">Used for auto-assigned class fee ledgers.</small></div>

            <div class="col-md-3"><label class="form-label">Father Name</label><input name="father_name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Mother Name</label><input name="mother_name" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Parent Phone</label><input name="phone_no" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Parent Email</label><input type="email" name="email" class="form-control"></div>

            <div class="col-md-3"><label class="form-label">Optional Scholarship %</label><input type="number" step="0.01" min="0" max="100" name="scholarship_percentage" class="form-control" value="0"></div>
            <div class="col-md-3"><label class="form-label">Admission Date</label><input type="date" name="admission_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-success w-100">Admit Student</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold"><i class="fa fa-search me-1"></i> Dynamic Search</div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students') }}" class="row g-2 js-live-search">
            <div class="col-md-3"><select name="class_id" class="form-select"><option value="">All Classes</option>@foreach($classes as $class)<option value="{{ $class->ClassID }}" {{ $selectedClassId == $class->ClassID ? 'selected' : '' }}>{{ $class->ClassName }}</option>@endforeach</select></div>
            <div class="col-md-7"><input name="search" value="{{ $search }}" class="form-control" placeholder="Search ID, full name, father/mother name, phone, email, class, section or address"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">Students</div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead><tr><th>ID</th><th>Name</th><th>Class</th><th>Father</th><th>Parent Contact</th><th>Address</th><th>Scholarship</th><th class="no-print">Actions</th></tr></thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->StudentID }}</td>
                    <td>{{ $student->First_Name }} {{ $student->Middle_Name }} {{ $student->Last_Name }}</td>
                    <td>{{ $student->ClassName }} / {{ $student->SectionName }}</td>
                    <td>{{ $student->Father_Name }}</td>
                    <td>{{ $student->Phone_No }}</td>
                    <td>{{ $student->Address }}</td>
                    <td>{{ $student->DiscountPercentage ?? 0 }}%</td>
                    <td class="no-print">
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.students.edit', $student->StudentID) }}">Edit</a>
                        <form action="{{ route('admin.students.destroy', $student->StudentID) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student and related fee data?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No students found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
