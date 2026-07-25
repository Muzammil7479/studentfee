<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $receipt->ReceiptNumber ?? 'SchoolM Receipt' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f4f6f9}.receipt{max-width:900px;margin:30px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 3px 15px rgba(0,0,0,.12)}
        .brand{font-weight:800;letter-spacing:.5px}.amount-row{font-size:1.05rem}.stamp{border:2px solid #198754;color:#198754;border-radius:8px;padding:8px 14px;display:inline-block;font-weight:700}
        @media print{.no-print{display:none!important}body{background:white}.receipt{box-shadow:none;margin:0;max-width:100%}}
    </style>
</head>
<body>
<div class="receipt">
    <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
        <div>
            <h2 class="brand mb-0">SchoolM</h2>
            <small>Student Fee Management System</small>
        </div>
        <div class="text-end">
            <h5 class="mb-0">Fee Receipt</h5>
            <strong>{{ $receipt->ReceiptNumber ?? ('PAY-' . $receipt->PaymentID) }}</strong><br>
            <small>Receipt Date: {{ $receipt->ReceiptDate ?? $receipt->PaymentDate }}</small><br>
            <span class="stamp mt-2">{{ $receipt->Status }}</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Student Details</h6>
            <p class="mb-1"><strong>ID:</strong> {{ $receipt->StudentID }}</p>
            <p class="mb-1"><strong>Name:</strong> {{ $receipt->First_Name }} {{ $receipt->Middle_Name }} {{ $receipt->Last_Name }}</p>
            <p class="mb-1"><strong>Class:</strong> {{ $receipt->ClassName }} / {{ $receipt->SectionName }}</p>
            <p class="mb-1"><strong>Father:</strong> {{ $receipt->Father_Name }}</p>
            <p class="mb-1"><strong>Parent Contact:</strong> {{ $receipt->Phone_No }}</p>
            <p class="mb-1"><strong>Address:</strong> {{ $receipt->Address }}</p>
        </div>
        <div class="col-md-6">
            <h6>Payment Details</h6>
            <p class="mb-1"><strong>Term:</strong> {{ $receipt->TermName ?? '-' }}</p>
            <p class="mb-1"><strong>Payment ID:</strong> {{ $receipt->PaymentID }}</p>
            <p class="mb-1"><strong>Payment Date:</strong> {{ $receipt->PaymentDate }}</p>
            <p class="mb-1"><strong>Method:</strong> {{ $receipt->PaymentMethod }}</p>
            <p class="mb-1"><strong>Reference:</strong> {{ $receipt->TransactionReference ?? '-' }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <tr><th>Total Fee</th><td class="text-end">Rs. {{ number_format($receipt->TotalAmount, 2) }}</td></tr>
        <tr><th>Scholarship / Discount</th><td class="text-end">Rs. {{ number_format($receipt->DiscountAmount, 2) }}</td></tr>
        <tr><th>Fine</th><td class="text-end">Rs. {{ number_format($receipt->FineAmount, 2) }}</td></tr>
        <tr class="amount-row"><th>Net Payable</th><td class="text-end fw-bold">Rs. {{ number_format($receipt->NetPayable, 2) }}</td></tr>
        <tr><th>Paid Before This Receipt</th><td class="text-end">Rs. {{ number_format($receipt->PaidBeforeThisReceipt, 2) }}</td></tr>
        <tr class="table-success amount-row"><th>Amount Paid in This Receipt</th><td class="text-end fw-bold">Rs. {{ number_format($receipt->AmountPaid, 2) }}</td></tr>
        <tr><th>Total Paid up to This Receipt</th><td class="text-end">Rs. {{ number_format($receipt->PaidUpToThisReceipt, 2) }}</td></tr>
        <tr class="table-warning amount-row"><th>Dues After This Receipt</th><td class="text-end fw-bold">Rs. {{ number_format($receipt->DuesAfterThisReceipt, 2) }}</td></tr>
        <tr><th>Current Total Paid</th><td class="text-end">Rs. {{ number_format($receipt->TotalPaid, 2) }}</td></tr>
        <tr><th>Current Remaining Dues</th><td class="text-end fw-bold">Rs. {{ number_format($receipt->CurrentDues, 2) }}</td></tr>
        @if($receipt->OverPaidAmount > 0)
            <tr><th>Overpaid</th><td class="text-end fw-bold">Rs. {{ number_format($receipt->OverPaidAmount, 2) }}</td></tr>
        @endif
    </table>

    <p class="text-muted small mb-5">This is a computer-generated SchoolM fee receipt. It can be printed or downloaded from the student/account payment history.</p>

    <div class="row mt-5">
        <div class="col-6 text-center"><div class="border-top pt-2">Accounts Signature</div></div>
        <div class="col-6 text-center"><div class="border-top pt-2">Parent/Student Signature</div></div>
    </div>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <a href="{{ route('account.receipt.download', $receipt->PaymentID) }}" class="btn btn-secondary">Download HTML Receipt</a>
        <a href="{{ route('account.dashboard', ['student_id' => $receipt->StudentID]) }}" class="btn btn-outline-dark">Back to Account Profile</a>
    </div>
</div>
</body>
</html>
