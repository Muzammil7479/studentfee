<?php

namespace App\Http\Controllers;

use App\Support\SchoolMData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrincipalController extends Controller
{
    public function index(Request $request)
    {
        $search = SchoolMData::normalizeSearch($request->search);
        $selectedClassId = $request->class_id;

        $classes = DB::table('class')->select('ClassID', 'ClassName', 'AcademicYear')->orderBy('ClassName')->get();
        $classMetrics = SchoolMData::classFinanceMetrics();
        $students = SchoolMData::searchStudents($search, $selectedClassId, 200);

        $financeSummary = SchoolMData::financeSummary();
        $totalStudents = DB::table('student')->count();
        $totalTeachers = DB::table('teachers')->count();
        $totalCollected = $financeSummary->total_paid;
        $totalOutstanding = $financeSummary->total_due;

        return view('principal_dashboard', compact(
            'classes', 'classMetrics', 'students', 'search', 'selectedClassId',
            'totalStudents', 'totalTeachers', 'totalCollected', 'totalOutstanding'
        ));
    }
}
