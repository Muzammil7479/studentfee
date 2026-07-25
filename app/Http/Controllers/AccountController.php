<?php

namespace App\Http\Controllers;

use App\Support\SchoolMData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $selectedClassId = $request->class_id;
        $search = SchoolMData::normalizeSearch($request->search_student);
        $selectedStudentId = $request->student_id;

        $classes = DB::table('class')->select('ClassID', 'ClassName', 'AcademicYear')->orderBy('ClassName')->get();
        $terms = DB::table('term')->select('TermID', 'TermName', 'StartDate', 'EndDate')->orderBy('StartDate')->get();
        $classOptions = SchoolMData::classNameOptions();

        $feePlans = DB::table('feestructure as fs')
            ->join('class as c', 'fs.ClassID', '=', 'c.ClassID')
            ->join('term as t', 'fs.TermID', '=', 't.TermID')
            ->select('fs.*', 'c.ClassName', 'c.AcademicYear', 't.TermName')
            ->orderBy('c.ClassName')
            ->orderBy('t.TermID')
            ->get();

        $feePlansByClass = $feePlans->groupBy('ClassID');

        $studentMatches = $search !== ''
            ? SchoolMData::searchStudents($search, $selectedClassId, 50)
            : collect();

        $studentData = null;
        if (!empty($selectedStudentId)) {
            $studentData = SchoolMData::studentProfileById((int) $selectedStudentId);
        }

        $feeRecords = $studentData ? SchoolMData::feeRecordsForStudent((int) $studentData->StudentID) : collect();
        $studentSummary = $studentData ? SchoolMData::studentFeeSummary((int) $studentData->StudentID) : null;
        $studentPaymentHistory = $studentData ? SchoolMData::allPaymentsForStudent((int) $studentData->StudentID) : collect();

        $financeSummary = SchoolMData::financeSummary();
        $totalOutstanding = $financeSummary->total_due;
        $totalCollected = $financeSummary->total_paid;
        $totalFeesAssigned = $financeSummary->total_fee;

        $recentPayments = DB::table('payment as p')
            ->join('studentfee as sf', 'p.StudentFeeID', '=', 'sf.StudentFeeID')
            ->join('student as s', 'sf.StudentID', '=', 's.StudentID')
            ->leftJoin('receipt as r', 'p.PaymentID', '=', 'r.PaymentID')
            ->select('p.*', 'r.ReceiptNumber', 's.StudentID', 's.First_Name', 's.Middle_Name', 's.Last_Name')
            ->orderByDesc('p.PaymentDate')
            ->orderByDesc('p.PaymentID')
            ->limit(8)
            ->get();

        return view('account', compact(
            'classes', 'terms', 'classOptions', 'feePlans', 'selectedClassId', 'search',
            'studentMatches', 'studentData', 'feeRecords', 'studentSummary', 'studentPaymentHistory',
            'totalOutstanding', 'totalCollected', 'totalFeesAssigned', 'recentPayments', 'feePlansByClass'
        ));
    }

    public function createClassPlan(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable',
            'class_name' => 'required|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'term_id' => 'required',
            'tuition_fee' => 'required|numeric|min:0',
            'exam_fee' => 'required|numeric|min:0',
            'transport_fee' => 'required|numeric|min:0',
            'misc_fee' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $classId = SchoolMData::getOrCreateClass($request->class_id, $request->class_name, $request->academic_year);

        $existingPlan = DB::table('feestructure')
            ->where('ClassID', $classId)
            ->where('TermID', $request->term_id)
            ->first();

        if ($existingPlan) {
            DB::table('feestructure')->where('FeeStructureID', $existingPlan->FeeStructureID)->update([
                'TuitionFee' => $request->tuition_fee,
                'ExamFee' => $request->exam_fee,
                'TransportFee' => $request->transport_fee,
                'MiscFee' => $request->misc_fee,
            ]);
            $feeStructureId = (int) $existingPlan->FeeStructureID;
        } else {
            $feeStructureId = SchoolMData::nextId('feestructure', 'FeeStructureID');
            DB::table('feestructure')->insert([
                'FeeStructureID' => $feeStructureId,
                'ClassID' => $classId,
                'TermID' => $request->term_id,
                'TuitionFee' => $request->tuition_fee,
                'ExamFee' => $request->exam_fee,
                'TransportFee' => $request->transport_fee,
                'MiscFee' => $request->misc_fee,
            ]);
        }

        $students = DB::table('student')->where('ClassID', $classId)->get();

        foreach ($students as $student) {
            SchoolMData::assignFeeStructureToStudent((int) $student->StudentID, (int) $feeStructureId, $request->due_date);
        }

        return back()->with('success', 'Fee structure saved by class. It was automatically assigned/recalculated for ' . $students->count() . ' existing student(s), and every future admitted student in this class will also receive this fee ledger automatically.');
    }

    public function updateClassPlan(Request $request, $feeStructureId)
    {
        $request->validate([
            'tuition_fee' => 'required|numeric|min:0',
            'exam_fee' => 'required|numeric|min:0',
            'transport_fee' => 'required|numeric|min:0',
            'misc_fee' => 'required|numeric|min:0',
        ]);

        $feeStructure = DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->first();
        if (!$feeStructure) {
            return back()->with('error', 'Fee structure not found.');
        }

        DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->update([
            'TuitionFee' => $request->tuition_fee,
            'ExamFee' => $request->exam_fee,
            'TransportFee' => $request->transport_fee,
            'MiscFee' => $request->misc_fee,
        ]);

        $updatedStructure = DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->first();
        $studentFees = DB::table('studentfee as sf')
            ->join('student as s', 'sf.StudentID', '=', 's.StudentID')
            ->select('sf.StudentFeeID', 's.ScholarshipID')
            ->where('sf.FeeStructureID', $feeStructureId)
            ->get();

        foreach ($studentFees as $record) {
            $discountAmount = SchoolMData::discountForScholarship($updatedStructure->TotalFee, $record->ScholarshipID);
            DB::table('studentfee')->where('StudentFeeID', $record->StudentFeeID)->update([
                'TotalAmount' => $updatedStructure->TotalFee,
                'DiscountAmount' => $discountAmount,
            ]);
            SchoolMData::recalculateStudentFee((int) $record->StudentFeeID);
        }

        return back()->with('success', 'Fee structure updated and all related student ledgers recalculated.');
    }

    public function deleteClassPlan($feeStructureId)
    {
        $feeStructure = DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->first();
        if (!$feeStructure) {
            return back()->with('error', 'Fee structure not found.');
        }

        $assignedCount = DB::table('studentfee')->where('FeeStructureID', $feeStructureId)->count();
        if ($assignedCount > 0) {
            return back()->with('error', 'This fee structure is assigned to students. Delete or move those student fee ledgers first. To protect database data, SchoolM did not delete it.');
        }

        DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->delete();

        return back()->with('success', 'Unused fee structure deleted successfully.');
    }

    public function applyScholarship(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $student = DB::table('student')->where('StudentID', $request->student_id)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $percentage = $request->filled('discount_percentage') ? (float) $request->discount_percentage : 0;
        $scholarshipId = SchoolMData::createScholarshipFromPercent($percentage);

        DB::table('student')->where('StudentID', $request->student_id)->update([
            'ScholarshipID' => $scholarshipId,
        ]);

        $studentFees = DB::table('studentfee as sf')
            ->join('feestructure as fs', 'sf.FeeStructureID', '=', 'fs.FeeStructureID')
            ->where('sf.StudentID', $request->student_id)
            ->select('sf.StudentFeeID', 'fs.TotalFee')
            ->get();

        foreach ($studentFees as $fee) {
            $discountAmount = $percentage > 0 ? SchoolMData::money(($fee->TotalFee * $percentage) / 100) : 0;
            DB::table('studentfee')->where('StudentFeeID', $fee->StudentFeeID)->update([
                'DiscountAmount' => $discountAmount,
            ]);
            SchoolMData::recalculateStudentFee((int) $fee->StudentFeeID);
        }

        return redirect()->route('account.dashboard', ['student_id' => $request->student_id])
            ->with('success', 'Scholarship updated. Percentage can be 0 to 100%.');
    }


    public function assignStudentFee(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'fee_structure_id' => 'required',
            'due_date' => 'nullable|date',
        ]);

        $student = DB::table('student')->where('StudentID', $request->student_id)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $feeStructure = DB::table('feestructure')->where('FeeStructureID', $request->fee_structure_id)->first();
        if (!$feeStructure) {
            return back()->with('error', 'Fee structure not found. Create a fee structure first, then assign it to the student.');
        }

        if ((int) $feeStructure->ClassID !== (int) $student->ClassID) {
            return redirect()->route('account.dashboard', ['student_id' => $student->StudentID])
                ->with('error', 'This fee structure belongs to another class. Select a fee structure for this student class.');
        }

        $studentFeeId = SchoolMData::assignFeeStructureToStudent((int) $student->StudentID, (int) $feeStructure->FeeStructureID, $request->due_date);
        if (!$studentFeeId) {
            return back()->with('error', 'Fee ledger could not be assigned.');
        }

        return redirect()->route('account.dashboard', ['student_id' => $student->StudentID])
            ->with('success', 'Fee ledger assigned/recalculated for this student. You can submit payment inside this same profile.');
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'student_fee_id' => 'required',
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,Bank Transfer,Credit Card,Debit Card,JazzCash,EasyPaisa',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $studentFee = DB::table('studentfee')->where('StudentFeeID', $request->student_fee_id)->first();
        if (!$studentFee) {
            return back()->with('error', 'Student fee record not found.');
        }

        $paymentId = SchoolMData::nextId('payment', 'PaymentID');
        DB::table('payment')->insert([
            'PaymentID' => $paymentId,
            'StudentFeeID' => $request->student_fee_id,
            'PaymentDate' => $request->payment_date,
            'AmountPaid' => $request->amount_paid,
            'PaymentMethod' => $request->payment_method,
            'TransactionReference' => $request->transaction_reference,
        ]);

        SchoolMData::ensureReceiptForPayment($paymentId, $request->payment_date);
        SchoolMData::recalculateStudentFee((int) $request->student_fee_id);

        return redirect()->route('account.receipt.print', $paymentId)
            ->with('success', 'Payment saved and receipt generated.');
    }

    public function updatePayment(Request $request, $paymentId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,Bank Transfer,Credit Card,Debit Card,JazzCash,EasyPaisa',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $payment = DB::table('payment')->where('PaymentID', $paymentId)->first();
        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        DB::table('payment')->where('PaymentID', $paymentId)->update([
            'PaymentDate' => $request->payment_date,
            'AmountPaid' => $request->amount_paid,
            'PaymentMethod' => $request->payment_method,
            'TransactionReference' => $request->transaction_reference,
        ]);

        $receipt = DB::table('receipt')->where('PaymentID', $paymentId)->first();
        if ($receipt) {
            DB::table('receipt')->where('PaymentID', $paymentId)->update([
                'ReceiptDate' => $request->payment_date,
            ]);
        } else {
            SchoolMData::ensureReceiptForPayment((int) $paymentId, $request->payment_date);
        }

        SchoolMData::recalculateStudentFee((int) $payment->StudentFeeID);
        $studentId = DB::table('studentfee')->where('StudentFeeID', $payment->StudentFeeID)->value('StudentID');

        return redirect()->route('account.dashboard', ['student_id' => $studentId])
            ->with('success', 'Payment updated and dues recalculated.');
    }

    public function deletePayment($paymentId)
    {
        $payment = DB::table('payment')->where('PaymentID', $paymentId)->first();
        if (!$payment) {
            return back()->with('error', 'Payment record not found.');
        }

        $studentId = DB::table('studentfee')->where('StudentFeeID', $payment->StudentFeeID)->value('StudentID');

        DB::table('receipt')->where('PaymentID', $paymentId)->delete();
        DB::table('payment')->where('PaymentID', $paymentId)->delete();

        SchoolMData::recalculateStudentFee((int) $payment->StudentFeeID);

        return redirect()->route('account.dashboard', ['student_id' => $studentId])
            ->with('success', 'Payment and its receipt deleted. Student dues have been recalculated.');
    }

    public function addFine(Request $request)
    {
        $request->validate([
            'student_fee_id' => 'required',
            'fine_amount' => 'required|numeric|min:1',
            'fine_reason' => 'nullable|string|max:255',
            'applied_date' => 'required|date',
        ]);

        $studentFee = DB::table('studentfee')->where('StudentFeeID', $request->student_fee_id)->first();
        if (!$studentFee) {
            return back()->with('error', 'Student fee record not found.');
        }

        DB::table('fine')->insert([
            'FineID' => SchoolMData::nextId('fine', 'FineID'),
            'FineAmount' => $request->fine_amount,
            'FineReason' => $request->fine_reason,
            'AppliedDate' => $request->applied_date,
            'StudentFeeID' => $request->student_fee_id,
        ]);

        SchoolMData::recalculateStudentFee((int) $request->student_fee_id);
        return back()->with('success', 'Fine added successfully.');
    }

    public function printReceipt($paymentId)
    {
        $receipt = SchoolMData::receiptDetails((int) $paymentId);
        if (!$receipt) {
            abort(404, 'Receipt not found.');
        }

        return response()->view('receipt_print', compact('receipt'));
    }

    public function downloadReceipt($paymentId)
    {
        $receipt = SchoolMData::receiptDetails((int) $paymentId);
        if (!$receipt) {
            abort(404, 'Receipt not found.');
        }

        $html = view('receipt_print', compact('receipt'))->render();
        $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $receipt->ReceiptNumber ?: ('receipt-' . $paymentId)) . '.html';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function calculateDiscount($totalFee, $scholarshipId): float
    {
        return SchoolMData::discountForScholarship($totalFee, $scholarshipId);
    }

    private function recalculateStudentFee(int $studentFeeId): void
    {
        SchoolMData::recalculateStudentFee($studentFeeId);
    }
}
