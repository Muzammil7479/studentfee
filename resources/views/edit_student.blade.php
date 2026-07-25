@extends('layouts.admin')

@section('title','Edit Student')
@section('heading','Edit Student')

@section('content')
<div class="card shadow-sm">
    <div class="card-header fw-bold">Update Student #{{ $student->StudentID }}</div>
    <div class="card-body">
        <form action="{{ route('admin.students.update', $student->StudentID) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-3"><label class="form-label">First Name</label><input name="first_name" value="{{ old('first_name', $student->First_Name) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Middle Name</label><input name="middle_name" value="{{ old('middle_name', $student->Middle_Name) }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Last Name</label><input name="last_name" value="{{ old('last_name', $student->Last_Name) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select" required><option {{ $student->Gender=='Male'?'selected':'' }}>Male</option><option {{ $student->Gender=='Female'?'selected':'' }}>Female</option></select></div>

            <div class="col-md-3"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->Date_of_Birth) }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Student Contact</label><input name="contact_no" value="{{ old('contact_no', $student->Contact_No) }}" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Student Address</label><input name="address" value="{{ old('address', $student->Address) }}" class="form-control"></div>

            <div class="col-md-3">
                <label class="form-label">Class</label>
                <input list="classOptionsList" name="class_name" class="form-control" value="{{ old('class_name', $student->ClassName) }}" required>
                <datalist id="classOptionsList">
                    @foreach($classOptions as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <small class="text-muted">If class changes, matching class fee structures will be checked/assigned automatically.</small>
            </div>
            <div class="col-md-2"><label class="form-label">Academic Year</label><input name="academic_year" class="form-control" value="{{ date('Y') }}-{{ date('Y')+1 }}"></div>
            <div class="col-md-2"><label class="form-label">Section</label><input name="section_name" value="{{ old('section_name', $student->SectionName ?: 'Section A') }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">Admission Date</label><input type="date" name="admission_date" value="{{ old('admission_date', $student->Admission_Date) }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">New Ledger Due Date</label><input type="date" name="due_date" class="form-control"><small class="text-muted">Optional for newly assigned ledgers.</small></div>
            <div class="col-md-3"><label class="form-label">Scholarship %</label><input type="number" step="0.01" min="0" max="100" name="scholarship_percentage" value="{{ old('scholarship_percentage', $student->DiscountPercentage ?? 0) }}" class="form-control"></div>

            <div class="col-md-3"><label class="form-label">Father Name</label><input name="father_name" value="{{ old('father_name', $student->Father_Name) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Mother Name</label><input name="mother_name" value="{{ old('mother_name', $student->Mother_Name) }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Parent Phone</label><input name="phone_no" value="{{ old('phone_no', $student->Phone_No) }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Parent Email</label><input type="email" name="email" value="{{ old('email', $student->Email) }}" class="form-control"></div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success">Update Student</button>
                <a class="btn btn-secondary" href="{{ route('admin.students') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
