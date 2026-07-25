@extends('layouts.admin')

@section('title','SchoolM Principal')
@section('heading','Principal Metrics')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card-box blue"><small>Total Students</small><h3>{{ $totalStudents }}</h3></div></div>
    <div class="col-md-3"><div class="card-box purple"><small>Total Teachers</small><h3>{{ $totalTeachers }}</h3></div></div>
    <div class="col-md-3"><div class="card-box green"><small>Collected Fee</small><h3>Rs. {{ number_format($totalCollected,2) }}</h3></div></div>
    <div class="col-md-3"><div class="card-box red"><small>Outstanding</small><h3>Rs. {{ number_format($totalOutstanding,2) }}</h3></div></div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Class-wise Students Admitted</div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead><tr><th>Class</th><th>Academic Year</th><th>Students Admitted</th><th>Total Fee</th><th>Discount</th><th>Fine</th><th>Payable</th><th>Paid</th><th>Total Dues</th></tr></thead>
            <tbody>
            @foreach($classMetrics as $metric)
                <tr>
                    <td>{{ $metric->ClassName }}</td>
                    <td>{{ $metric->AcademicYear }}</td>
                    <td><span class="badge bg-primary">{{ $metric->total_students }}</span></td>
                    <td>Rs. {{ number_format($metric->total_fee,2) }}</td>
                    <td>Rs. {{ number_format($metric->total_discount,2) }}</td>
                    <td>Rs. {{ number_format($metric->total_fine,2) }}</td>
                    <td>Rs. {{ number_format($metric->total_payable,2) }}</td>
                    <td>Rs. {{ number_format($metric->total_paid,2) }}</td>
                    <td>Rs. {{ number_format($metric->total_dues,2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold">Dynamic Search Students</div>
    <div class="card-body">
        <form method="GET" action="{{ route('principal.dashboard') }}" class="row g-2 js-live-search">
            <div class="col-md-3"><select name="class_id" class="form-select"><option value="">All Classes</option>@foreach($classes as $class)<option value="{{ $class->ClassID }}" {{ $selectedClassId == $class->ClassID ? 'selected' : '' }}>{{ $class->ClassName }}</option>@endforeach</select></div>
            <div class="col-md-7"><input name="search" value="{{ $search }}" class="form-control" placeholder="Search ID, full name, father/mother name, parent contact, email, class, section or address"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-bold">Student Details with Parent Contact and Address</div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead><tr><th>ID</th><th>Student</th><th>Class</th><th>Father</th><th>Mother</th><th>Parent Contact</th><th>Email</th><th>Address</th></tr></thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->StudentID }}</td>
                    <td>{{ $student->First_Name }} {{ $student->Middle_Name }} {{ $student->Last_Name }}</td>
                    <td>{{ $student->ClassName }} / {{ $student->SectionName }}</td>
                    <td>{{ $student->Father_Name }}</td>
                    <td>{{ $student->Mother_Name }}</td>
                    <td>{{ $student->Phone_No }}</td>
                    <td>{{ $student->Email }}</td>
                    <td>{{ $student->Address }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No student data found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
