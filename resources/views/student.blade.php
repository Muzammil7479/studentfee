@extends('layouts.admin')

@section('title','SchoolM Student')
@section('heading','Student Portal')

@section('content')
<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold"><i class="fa fa-search me-1"></i> Student Search</div>
    <div class="card-body">
        <form action="{{ route('student.dashboard') }}" method="GET" class="row g-2 js-live-search">
            <div class="col-md-10">
                <input name="search_query" value="{{ $search }}" class="form-control" placeholder="Search student ID, full name, father name, class, phone or address">
                <small class="text-muted">Type any keyword. If more than one student matches, all related profiles are shown.</small>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
        </form>
    </div>
</div>

@if(!$searchPerformed)
    <div class="alert alert-info">Search your student ID or name to view your profile, total fee, paid amount, dues, payment history and receipts.</div>
@elseif($studentMatches->count() > 1 && !$studentData)
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Multiple related students found</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Class</th><th>Father</th><th>Parent Phone</th><th>Address</th><th>Open</th></tr></thead>
                <tbody>
                @foreach($studentMatches as $st)
                    <tr>
                        <td>{{ $st->StudentID }}</td>
                        <td>{{ $st->First_Name }} {{ $st->Middle_Name }} {{ $st->Last_Name }}</td>
                        <td>{{ $st->ClassName }} / {{ $st->SectionName }}</td>
                        <td>{{ $st->Father_Name }}</td>
                        <td>{{ $st->Phone_No }}</td>
                        <td>{{ $st->Address }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('student.dashboard', ['student_id'=>$st->StudentID, 'search_query'=>$search]) }}">Open Profile</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif($searchPerformed && !$studentData)
    <div class="alert alert-danger">No student found for this keyword.</div>
@else
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-user-graduate me-1"></i> My Profile</div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-3"><small class="text-muted">Student ID</small><div class="fw-bold">{{ $studentData->StudentID }}</div></div>
                <div class="col-md-3"><small class="text-muted">Name</small><div class="fw-bold">{{ $studentData->First_Name }} {{ $studentData->Middle_Name }} {{ $studentData->Last_Name }}</div></div>
                <div class="col-md-3"><small class="text-muted">Class / Section</small><div class="fw-bold">{{ $studentData->ClassName }} / {{ $studentData->SectionName }}</div></div>
                <div class="col-md-3"><small class="text-muted">Scholarship</small><div class="fw-bold">{{ $studentData->DiscountPercentage ?? 0 }}%</div></div>
                <div class="col-md-3"><small class="text-muted">Father</small><div>{{ $studentData->Father_Name }}</div></div>
                <div class="col-md-3"><small class="text-muted">Parent Contact</small><div>{{ $studentData->Phone_No }}</div></div>
                <div class="col-md-6"><small class="text-muted">Address</small><div>{{ $studentData->Address }}</div></div>
            </div>

            @if($studentSummary)
                <div class="row g-3">
                    <div class="col-md-2"><div class="border rounded p-2"><small>Total Fee</small><div class="fw-bold">Rs. {{ number_format($studentSummary->total_fee,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Discount</small><div class="fw-bold text-danger">Rs. {{ number_format($studentSummary->total_discount,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Fine</small><div class="fw-bold text-warning">Rs. {{ number_format($studentSummary->total_fine,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Payable</small><div class="fw-bold">Rs. {{ number_format($studentSummary->total_payable,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Paid</small><div class="fw-bold text-success">Rs. {{ number_format($studentSummary->total_paid,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Dues</small><div class="fw-bold text-danger">Rs. {{ number_format($studentSummary->total_due,2) }}</div></div></div>
                </div>
            @endif
        </div>
    </div>

    @forelse($feeRecords as $fee)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between">
                <strong>{{ $fee->TermName ?? 'Fee' }} Fee History</strong>
                <span class="badge {{ $fee->Status == 'Paid' ? 'bg-success' : ($fee->Status == 'Partially Paid' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $fee->Status }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col"><small>Total</small><div class="fw-bold">Rs. {{ number_format($fee->TotalAmount,2) }}</div></div>
                    <div class="col"><small>Discount</small><div class="fw-bold text-danger">Rs. {{ number_format($fee->DiscountAmount,2) }}</div></div>
                    <div class="col"><small>Fine</small><div class="fw-bold text-warning">Rs. {{ number_format($fee->FineAmount,2) }}</div></div>
                    <div class="col"><small>Payable</small><div class="fw-bold">Rs. {{ number_format($fee->PayableAmount,2) }}</div></div>
                    <div class="col"><small>Paid</small><div class="fw-bold text-success">Rs. {{ number_format($fee->PaidAmount,2) }}</div></div>
                    <div class="col"><small>Dues</small><div class="fw-bold text-danger">Rs. {{ number_format($fee->RemainingBalance,2) }}</div></div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No fee record found for this student.</div>
    @endforelse

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fa fa-receipt me-1"></i> My Payment History and Downloadable Receipts</div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead><tr><th>Date</th><th>Term</th><th>Amount</th><th>Method</th><th>Reference</th><th>Receipt No.</th><th class="no-print">Receipt</th></tr></thead>
                <tbody>
                @forelse($paymentHistory as $payment)
                    <tr>
                        <td>{{ $payment->PaymentDate }}</td>
                        <td>{{ $payment->TermName ?? '-' }}</td>
                        <td>Rs. {{ number_format($payment->AmountPaid,2) }}</td>
                        <td>{{ $payment->PaymentMethod }}</td>
                        <td>{{ $payment->TransactionReference ?? '-' }}</td>
                        <td>{{ $payment->ReceiptNumber ?? 'Auto generated on print' }}</td>
                        <td class="no-print">
                            <a href="{{ route('student.receipt.print', $payment->PaymentID) }}" class="btn btn-sm btn-outline-primary">Print</a>
                            <a href="{{ route('student.receipt.download', $payment->PaymentID) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No payments yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
