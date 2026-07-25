<?php

namespace App\Http\Controllers;

use App\Support\SchoolMData;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = SchoolMData::normalizeSearch($request->search_query);
        $selectedStudentId = $request->student_id;
        $searchPerformed = $search !== '' || !empty($selectedStudentId);

        $studentMatches = $search !== '' ? SchoolMData::searchStudents($search, null, 50) : collect();
        $studentData = null;

        if (!empty($selectedStudentId)) {
            $studentData = SchoolMData::studentProfileById((int) $selectedStudentId);
        } elseif ($studentMatches->count() === 1) {
            $studentData = $studentMatches->first();
        }

        $feeRecords = $studentData ? SchoolMData::feeRecordsForStudent((int) $studentData->StudentID) : collect();
        $studentSummary = $studentData ? SchoolMData::studentFeeSummary((int) $studentData->StudentID) : null;
        $paymentHistory = $studentData ? SchoolMData::allPaymentsForStudent((int) $studentData->StudentID) : collect();

        return view('student', compact(
            'studentData', 'feeRecords', 'studentSummary', 'paymentHistory',
            'searchPerformed', 'studentMatches', 'search'
        ));
    }
}
