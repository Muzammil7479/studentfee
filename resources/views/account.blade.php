@extends('layouts.admin')

@section('title','SchoolM Accounts')
@section('heading','Accounts Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-box blue">
            <small>Total Fees Assigned</small>
            <h3>Rs. {{ number_format($totalFeesAssigned, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-box green">
            <small>Total Revenue Collected</small>
            <h3>Rs. {{ number_format($totalCollected, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-box red">
            <small>Total Active Dues</small>
            <h3>Rs. {{ number_format($totalOutstanding, 2) }}</h3>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold"><i class="fa fa-search me-1"></i> Dynamic Student Search</div>
    <div class="card-body">
        <form action="{{ route('account.dashboard') }}" method="GET" class="row g-2 js-live-search">
            <div class="col-md-3">
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->ClassID }}" {{ $selectedClassId == $class->ClassID ? 'selected' : '' }}>
                            {{ $class->ClassName }} - {{ $class->AcademicYear }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <input type="text" name="search_student" value="{{ $search }}" class="form-control" placeholder="Search by student ID, full name, father name, phone, email, class or address">
                <small class="text-muted">Example: Ali Khan, 0300, Grade 10, father name, address keyword.</small>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="card shadow-sm mb-4 no-print">
    <div class="card-header fw-bold"><i class="fa fa-layer-group me-1"></i> Apply Fee Structure to Class</div>
    <div class="card-body">
        <form action="{{ route('account.createClassPlan') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Class from Playgroup to 10th</label>
                <input list="classOptionsList" name="class_name" class="form-control" value="Grade 10" required>
                <datalist id="classOptionsList">
                    @foreach($classOptions as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <small class="text-muted">If class does not exist, SchoolM creates it in the existing class table. New students admitted in this class will receive this fee structure automatically.</small>
            </div>
            <div class="col-md-2">
                <label class="form-label">Academic Year</label>
                <input name="academic_year" class="form-control" value="{{ date('Y') }}-{{ date('Y')+1 }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Term</label>
                <select name="term_id" class="form-select" required>
                    @foreach($terms as $term)
                        <option value="{{ $term->TermID }}">{{ $term->TermName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Tuition</label><input type="number" step="0.01" name="tuition_fee" class="form-control" required></div>
            <div class="col-md-1"><label class="form-label">Exam</label><input type="number" step="0.01" name="exam_fee" class="form-control" value="0" required></div>
            <div class="col-md-1"><label class="form-label">Transport</label><input type="number" step="0.01" name="transport_fee" class="form-control" value="0" required></div>
            <div class="col-md-1"><label class="form-label">Misc</label><input type="number" step="0.01" name="misc_fee" class="form-control" value="0" required></div>
            <div class="col-md-3"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"></div>
            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-success w-100">Save & Auto Apply by Class</button></div>
        </form>
    </div>
</div>
@endif

@if($search !== '')
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Search Results</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Class</th><th>Father</th><th>Phone</th><th>Address</th><th>Open</th></tr></thead>
                <tbody>
                @forelse($studentMatches as $st)
                    <tr class="{{ $studentData && $studentData->StudentID == $st->StudentID ? 'table-primary' : '' }}">
                        <td>{{ $st->StudentID }}</td>
                        <td>{{ $st->First_Name }} {{ $st->Middle_Name }} {{ $st->Last_Name }}</td>
                        <td>{{ $st->ClassName }} / {{ $st->SectionName }}</td>
                        <td>{{ $st->Father_Name }}</td>
                        <td>{{ $st->Phone_No }}</td>
                        <td>{{ $st->Address }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('account.dashboard', ['student_id'=>$st->StudentID, 'search_student'=>$search, 'class_id'=>$selectedClassId]) }}#studentProfile">Open Profile</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No related accounts found for this keyword.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if($studentData)
    <div class="card shadow-sm mb-4" id="studentProfile">
        <div class="card-header fw-bold"><i class="fa fa-user me-1"></i> Student Account Profile</div>
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
                <div class="row g-3 mb-4">
                    <div class="col-md-2"><div class="border rounded p-2"><small>Total Fee</small><div class="fw-bold">Rs. {{ number_format($studentSummary->total_fee,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Discount</small><div class="fw-bold text-danger">Rs. {{ number_format($studentSummary->total_discount,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Fine</small><div class="fw-bold text-warning">Rs. {{ number_format($studentSummary->total_fine,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Payable</small><div class="fw-bold">Rs. {{ number_format($studentSummary->total_payable,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Paid</small><div class="fw-bold text-success">Rs. {{ number_format($studentSummary->total_paid,2) }}</div></div></div>
                    <div class="col-md-2"><div class="border rounded p-2"><small>Dues</small><div class="fw-bold text-danger">Rs. {{ number_format($studentSummary->total_due,2) }}</div></div></div>
                </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="row g-3 no-print">
                <div class="col-lg-5">
                    <form action="{{ route('account.applyScholarship') }}" method="POST" class="border rounded p-3 h-100 bg-light">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $studentData->StudentID }}">
                        <h6 class="fw-bold mb-3"><i class="fa fa-percent me-1"></i> Optional Scholarship</h6>
                        <label class="form-label">Scholarship %</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control mb-2" value="{{ $studentData->DiscountPercentage ?? 0 }}">
                        <small class="text-muted d-block mb-2">Use 0 for no scholarship. Any value from 0 to 100 is allowed.</small>
                        <button class="btn btn-warning w-100">Apply Scholarship</button>
                    </form>
                </div>

                <div class="col-lg-7">
                    <form action="{{ route('account.assignStudentFee') }}" method="POST" class="border rounded p-3 h-100 bg-light">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $studentData->StudentID }}">
                        <h6 class="fw-bold mb-3"><i class="fa fa-file-invoice-dollar me-1"></i> Assign Fee Ledger to This Student</h6>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <label class="form-label">Fee Structure</label>
                                @php
                                    $studentClassFeePlans = $feePlans->where('ClassID', $studentData->ClassID);
                                @endphp
                                <select name="fee_structure_id" class="form-select" required>
                                    <option value="">Select fee structure for {{ $studentData->ClassName }}</option>
                                    @forelse($studentClassFeePlans as $plan)
                                        <option value="{{ $plan->FeeStructureID }}">
                                            {{ $plan->ClassName }} - {{ $plan->TermName }} | Rs. {{ number_format($plan->TotalFee, 2) }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No fee structure found for this student's class</option>
                                    @endforelse
                                </select>
                                <small class="text-muted">Only this student class fee structures are shown. Create the class fee structure above if it is not listed.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3 w-100">Assign Fee Ledger</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card border mt-4" id="paymentHistory">
                <div class="card-header fw-bold bg-white"><i class="fa fa-clock-rotate-left me-1"></i> Payment History in Student Profile</div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Date</th><th>Term</th><th>Amount</th><th>Method</th><th>Reference</th><th>Receipt No.</th><th class="no-print">CRUD / Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($studentPaymentHistory as $payment)
                            <form id="payment-update-{{ $payment->PaymentID }}" action="{{ route('account.payment.update', $payment->PaymentID) }}" method="POST">
                                @csrf
                                @method('PUT')
                            </form>
                            <tr>
                                <td><input form="payment-update-{{ $payment->PaymentID }}" type="date" name="payment_date" value="{{ $payment->PaymentDate }}" class="form-control form-control-sm" required></td>
                                <td>{{ $payment->TermName ?? '-' }}</td>
                                <td><input form="payment-update-{{ $payment->PaymentID }}" type="number" step="0.01" min="1" name="amount_paid" value="{{ $payment->AmountPaid }}" class="form-control form-control-sm" required></td>
                                <td>
                                    <select form="payment-update-{{ $payment->PaymentID }}" name="payment_method" class="form-select form-select-sm" required>
                                        @foreach(['Cash','Bank Transfer','Credit Card','Debit Card','JazzCash','EasyPaisa'] as $method)
                                            <option value="{{ $method }}" {{ $payment->PaymentMethod == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input form="payment-update-{{ $payment->PaymentID }}" name="transaction_reference" value="{{ $payment->TransactionReference }}" class="form-control form-control-sm" placeholder="Reference"></td>
                                <td>{{ $payment->ReceiptNumber ?? 'Auto generated on print' }}</td>
                                <td class="no-print text-nowrap">
                                    @if(auth()->user()->isAdmin())
                                        <button form="payment-update-{{ $payment->PaymentID }}" class="btn btn-sm btn-success">Update</button>
                                    @endif
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('account.receipt.print', $payment->PaymentID) }}">Print</a>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('account.receipt.download', $payment->PaymentID) }}">Download</a>
                                    @if(auth()->user()->isAdmin())
                                        <form action="{{ route('account.payment.delete', $payment->PaymentID) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment and its receipt?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No payment history yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @forelse($feeRecords as $fee)
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between">
                <strong>{{ $fee->TermName ?? 'Fee' }} Ledger</strong>
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

                <div class="row g-3 no-print mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('account.addPayment') }}" method="POST" class="border rounded p-3 bg-light h-100">
                            @csrf
                            <input type="hidden" name="student_fee_id" value="{{ $fee->StudentFeeID }}">
                            <h6>Add Payment & Generate Receipt</h6>
                            <label class="form-label">Fee Submission Amount</label>
                            <input type="number" step="0.01" min="1" max="{{ max($fee->RemainingBalance, $fee->PayableAmount) }}" name="amount_paid" class="form-control mb-2" placeholder="Amount paid" value="{{ $fee->RemainingBalance > 0 ? $fee->RemainingBalance : '' }}" required>
                            <div class="row g-2 mb-2">
                                <div class="col"><select name="payment_method" class="form-select" required><option>Cash</option><option>Bank Transfer</option><option>Credit Card</option><option>Debit Card</option><option>JazzCash</option><option>EasyPaisa</option></select></div>
                                <div class="col"><input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            </div>
                            <input name="transaction_reference" class="form-control mb-2" placeholder="Transaction reference">
                            <button class="btn btn-success w-100">Submit Fee & Open Receipt</button>
                        </form>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div class="col-md-6">
                        <form action="{{ route('account.addFine') }}" method="POST" class="border rounded p-3 bg-light h-100">
                            @csrf
                            <input type="hidden" name="student_fee_id" value="{{ $fee->StudentFeeID }}">
                            <h6>Add Fine</h6>
                            <input type="number" step="0.01" name="fine_amount" class="form-control mb-2" placeholder="Fine amount" required>
                            <input name="fine_reason" class="form-control mb-2" placeholder="Fine reason">
                            <input type="date" name="applied_date" class="form-control mb-2" value="{{ date('Y-m-d') }}" required>
                            <button class="btn btn-danger">Save Fine</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <strong>No fee ledger assigned to this student yet.</strong><br>
            Use the <b>Assign Fee Ledger to This Student</b> form inside the student profile above, then the fee submission form will appear for this student.
        </div>
    @endforelse

@endif

@if(auth()->user()->isAdmin())
<div class="card shadow-sm no-print">
    <div class="card-header fw-bold">Existing Fee Structures CRUD - Grouped by Class</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead>
                <tr><th>Class</th><th>Term</th><th>Tuition</th><th>Exam</th><th>Transport</th><th>Misc</th><th>Total</th><th>Action</th></tr>
            </thead>
            <tbody>
            @forelse($feePlansByClass as $classId => $plans)
                @php $firstPlan = $plans->first(); @endphp
                <tr class="table-primary">
                    <td colspan="8" class="fw-bold">
                        {{ $firstPlan->ClassName }} - {{ $firstPlan->AcademicYear }}
                        <span class="badge bg-dark ms-2">{{ $plans->count() }} fee structure(s)</span>
                    </td>
                </tr>
                @foreach($plans as $plan)
                    <form id="fee-plan-update-{{ $plan->FeeStructureID }}" action="{{ route('account.feeStructure.update', $plan->FeeStructureID) }}" method="POST">
                        @csrf
                        @method('PUT')
                    </form>
                    <tr>
                        <td>{{ $plan->ClassName }}</td>
                        <td>{{ $plan->TermName }}</td>
                        <td><input form="fee-plan-update-{{ $plan->FeeStructureID }}" type="number" step="0.01" min="0" name="tuition_fee" value="{{ $plan->TuitionFee }}" class="form-control form-control-sm" required></td>
                        <td><input form="fee-plan-update-{{ $plan->FeeStructureID }}" type="number" step="0.01" min="0" name="exam_fee" value="{{ $plan->ExamFee }}" class="form-control form-control-sm" required></td>
                        <td><input form="fee-plan-update-{{ $plan->FeeStructureID }}" type="number" step="0.01" min="0" name="transport_fee" value="{{ $plan->TransportFee }}" class="form-control form-control-sm" required></td>
                        <td><input form="fee-plan-update-{{ $plan->FeeStructureID }}" type="number" step="0.01" min="0" name="misc_fee" value="{{ $plan->MiscFee }}" class="form-control form-control-sm" required></td>
                        <td>Rs. {{ number_format($plan->TotalFee,2) }}</td>
                        <td class="text-nowrap">
                            <button form="fee-plan-update-{{ $plan->FeeStructureID }}" class="btn btn-sm btn-success">Update</button>
                            <form action="{{ route('account.feeStructure.delete', $plan->FeeStructureID) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unused fee structure? Assigned fee structures are protected.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="text-center text-muted">No fee structures created yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
