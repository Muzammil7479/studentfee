<?php

namespace App\Http\Controllers;

use App\Support\SchoolMData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $selectedClassId = $request->class_id;
        $search = SchoolMData::normalizeSearch($request->search);

        $classes = DB::table('class')->select('ClassID', 'ClassName', 'AcademicYear')->orderBy('ClassName')->get();
        $sections = DB::table('section')->select('SectionID', 'SectionName', 'ClassID')->orderBy('SectionName')->get();
        $terms = DB::table('term')->select('TermID', 'TermName', 'StartDate', 'EndDate')->orderBy('StartDate')->get();
        $classOptions = SchoolMData::classNameOptions();

        $students = SchoolMData::searchStudents($search, $selectedClassId, 100);

        $classTerms = DB::table('feestructure as fs')
            ->join('class as c', 'fs.ClassID', '=', 'c.ClassID')
            ->join('term as t', 'fs.TermID', '=', 't.TermID')
            ->select('fs.*', 'c.ClassName', 'c.AcademicYear', 't.TermName')
            ->orderBy('c.ClassName')
            ->get();

        return view('admin_students', compact(
            'classes', 'sections', 'terms', 'classOptions', 'classTerms', 'students', 'selectedClassId', 'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'class_name' => 'required|string|max:50',
            'term_id' => 'nullable',
            'father_name' => 'required|string|max:100',
            'phone_no' => 'required|string|max:20',
            'scholarship_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $classId = SchoolMData::getOrCreateClass($request->class_id, $request->class_name, $request->academic_year);
        $sectionId = SchoolMData::getOrCreateSection($classId, $request->section_id, $request->section_name ?: 'Section A');
        $scholarshipId = SchoolMData::createScholarshipFromPercent($request->filled('scholarship_percentage') ? (float) $request->scholarship_percentage : 0);

        $parentId = DB::table('parent')->insertGetId([
            'Father_Name' => $request->father_name,
            'Mother_Name' => $request->mother_name,
            'Phone_No' => $request->phone_no,
            'Email' => $request->email,
        ]);

        $studentId = DB::table('student')->insertGetId([
            'First_Name' => $request->first_name,
            'Middle_Name' => $request->middle_name,
            'Last_Name' => $request->last_name,
            'Gender' => $request->gender,
            'Date_of_Birth' => $request->date_of_birth,
            'Contact_No' => $request->contact_no,
            'Address' => $request->address,
            'Admission_Date' => $request->admission_date ?: now()->format('Y-m-d'),
            'ClassID' => $classId,
            'SectionID' => $sectionId,
            'ParentID' => $parentId,
            'ScholarshipID' => $scholarshipId,
        ]);

        $assignedCount = SchoolMData::assignAllClassFeeStructuresToStudent((int) $studentId, (int) $classId, $request->due_date);

        if ($assignedCount > 0) {
            return back()->with('success', 'Student admitted successfully in ' . $request->class_name . '. ' . $assignedCount . ' class fee structure(s) were automatically assigned to this student.');
        }

        return back()->with('success', 'Student admitted successfully in ' . $request->class_name . '. No fee structure exists for this class yet. When Accounts creates a fee structure for this class, it will be applied automatically.');
    }

    public function edit($id)
    {
        $student = DB::table('student as s')
            ->leftJoin('parent as p', 's.ParentID', '=', 'p.ParentID')
            ->leftJoin('class as c', 's.ClassID', '=', 'c.ClassID')
            ->leftJoin('section as sec', 's.SectionID', '=', 'sec.SectionID')
            ->leftJoin('scholarship as sch', 's.ScholarshipID', '=', 'sch.ScholarshipID')
            ->where('s.StudentID', $id)
            ->select('s.*', 'p.Father_Name', 'p.Mother_Name', 'p.Phone_No', 'p.Email', 'c.ClassName', 'sec.SectionName', 'sch.DiscountPercentage')
            ->first();

        if (!$student) {
            return redirect()->route('admin.students')->with('error', 'Student not found.');
        }

        $classes = DB::table('class')->select('ClassID', 'ClassName', 'AcademicYear')->orderBy('ClassName')->get();
        $sections = DB::table('section')->select('SectionID', 'SectionName', 'ClassID')->orderBy('SectionName')->get();
        $terms = DB::table('term')->select('TermID', 'TermName')->orderBy('TermID')->get();
        $classOptions = SchoolMData::classNameOptions();

        return view('edit_student', compact('student', 'classes', 'sections', 'terms', 'classOptions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'class_name' => 'required|string|max:50',
            'father_name' => 'required|string|max:100',
            'phone_no' => 'required|string|max:20',
            'scholarship_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $student = DB::table('student')->where('StudentID', $id)->first();
        if (!$student) {
            return redirect()->route('admin.students')->with('error', 'Student not found.');
        }

        $classId = SchoolMData::getOrCreateClass($request->class_id, $request->class_name, $request->academic_year);
        $sectionId = SchoolMData::getOrCreateSection($classId, $request->section_id, $request->section_name ?: 'Section A');
        $scholarshipId = SchoolMData::createScholarshipFromPercent($request->filled('scholarship_percentage') ? (float) $request->scholarship_percentage : 0);

        DB::table('student')->where('StudentID', $id)->update([
            'First_Name' => $request->first_name,
            'Middle_Name' => $request->middle_name,
            'Last_Name' => $request->last_name,
            'Gender' => $request->gender,
            'Date_of_Birth' => $request->date_of_birth,
            'Contact_No' => $request->contact_no,
            'Address' => $request->address,
            'Admission_Date' => $request->admission_date,
            'ClassID' => $classId,
            'SectionID' => $sectionId,
            'ScholarshipID' => $scholarshipId,
        ]);

        DB::table('parent')->where('ParentID', $student->ParentID)->update([
            'Father_Name' => $request->father_name,
            'Mother_Name' => $request->mother_name,
            'Phone_No' => $request->phone_no,
            'Email' => $request->email,
        ]);

        $this->refreshStudentDiscounts((int) $id, $scholarshipId);
        $assignedCount = SchoolMData::assignAllClassFeeStructuresToStudent((int) $id, (int) $classId, $request->due_date);

        return redirect()->route('admin.students')->with('success', 'Student updated successfully. Matching class fee structure(s) checked/assigned automatically: ' . $assignedCount . '.');
    }

    public function destroy($id)
    {
        $student = DB::table('student')->where('StudentID', $id)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $studentFeeIds = DB::table('studentfee')->where('StudentID', $id)->pluck('StudentFeeID');
        $paymentIds = DB::table('payment')->whereIn('StudentFeeID', $studentFeeIds)->pluck('PaymentID');

        DB::table('receipt')->whereIn('PaymentID', $paymentIds)->delete();
        DB::table('payment')->whereIn('StudentFeeID', $studentFeeIds)->delete();
        DB::table('fine')->whereIn('StudentFeeID', $studentFeeIds)->delete();
        DB::table('studentfee')->where('StudentID', $id)->delete();
        DB::table('student')->where('StudentID', $id)->delete();
        if ($student->ParentID) {
            DB::table('parent')->where('ParentID', $student->ParentID)->delete();
        }

        return back()->with('success', 'Student and related records deleted.');
    }

    private function calculateDiscount($totalFee, $scholarshipId): float
    {
        return SchoolMData::discountForScholarship($totalFee, $scholarshipId);
    }

    private function refreshStudentDiscounts(int $studentId, ?int $scholarshipId): void
    {
        $fees = DB::table('studentfee as sf')
            ->join('feestructure as fs', 'sf.FeeStructureID', '=', 'fs.FeeStructureID')
            ->where('sf.StudentID', $studentId)
            ->select('sf.StudentFeeID', 'fs.TotalFee')
            ->get();

        foreach ($fees as $fee) {
            $discount = $this->calculateDiscount($fee->TotalFee, $scholarshipId);
            DB::table('studentfee')->where('StudentFeeID', $fee->StudentFeeID)->update([
                'DiscountAmount' => $discount,
            ]);
            SchoolMData::recalculateStudentFee((int) $fee->StudentFeeID);
        }
    }
}
