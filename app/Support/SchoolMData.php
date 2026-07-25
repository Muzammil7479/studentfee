<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolMData
{
    public static function classNameOptions(): array
    {
        return [
            'Playgroup', 'Nursery', 'KG',
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5',
            'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
        ];
    }

    public static function currentAcademicYear(): string
    {
        $year = (int) date('Y');
        return $year . '-' . ($year + 1);
    }

    public static function nextId(string $table, string $column): int
    {
        return ((int) DB::table($table)->max($column)) + 1;
    }

    public static function money($value): float
    {
        return round((float) ($value ?? 0), 2);
    }

    public static function normalizeSearch(?string $keyword): string
    {
        $keyword = trim((string) $keyword);
        $keyword = preg_replace('/\s+/', ' ', $keyword);
        return $keyword ?: '';
    }

    public static function searchTokens(?string $keyword): array
    {
        $keyword = self::normalizeSearch($keyword);
        if ($keyword === '') {
            return [];
        }

        return array_values(array_filter(explode(' ', mb_strtolower($keyword)), function ($part) {
            return trim($part) !== '';
        }));
    }

    public static function getOrCreateClass(?string $classId = null, ?string $className = null, ?string $academicYear = null): int
    {
        if (!empty($classId)) {
            $exists = DB::table('class')->where('ClassID', $classId)->first();
            if ($exists) {
                return (int) $exists->ClassID;
            }
        }

        $className = trim((string) $className);
        if ($className === '') {
            $className = 'Grade 10';
        }

        $existing = DB::table('class')
            ->whereRaw('LOWER(ClassName) = ?', [mb_strtolower($className)])
            ->first();

        if ($existing) {
            return (int) $existing->ClassID;
        }

        $newId = self::nextId('class', 'ClassID');
        DB::table('class')->insert([
            'ClassID' => $newId,
            'ClassName' => $className,
            'AcademicYear' => $academicYear ?: self::currentAcademicYear(),
        ]);

        return $newId;
    }

    public static function getOrCreateSection(int $classId, ?string $sectionId = null, ?string $sectionName = null): int
    {
        if (!empty($sectionId)) {
            $exists = DB::table('section')->where('SectionID', $sectionId)->first();
            if ($exists) {
                return (int) $exists->SectionID;
            }
        }

        $sectionName = trim((string) ($sectionName ?: 'Section A'));
        $existing = DB::table('section')
            ->where('ClassID', $classId)
            ->whereRaw('LOWER(SectionName) = ?', [mb_strtolower($sectionName)])
            ->first();

        if ($existing) {
            return (int) $existing->SectionID;
        }

        $newId = self::nextId('section', 'SectionID');
        DB::table('section')->insert([
            'SectionID' => $newId,
            'SectionName' => $sectionName,
            'ClassID' => $classId,
        ]);

        return $newId;
    }

    public static function createScholarshipFromPercent(?float $percentage): ?int
    {
        if ($percentage === null || $percentage <= 0) {
            return null;
        }

        $percentage = min(max($percentage, 0), 100);
        $percentageLabel = rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.');
        $name = 'Custom ' . $percentageLabel . '% Scholarship';

        $existing = DB::table('scholarship')
            ->where('ScholarshipName', $name)
            ->where('DiscountPercentage', $percentage)
            ->first();

        if ($existing) {
            return (int) $existing->ScholarshipID;
        }

        $newId = self::nextId('scholarship', 'ScholarshipID');
        DB::table('scholarship')->insert([
            'ScholarshipID' => $newId,
            'ScholarshipName' => $name,
            'DiscountPercentage' => $percentage,
            'Description' => 'Optional scholarship entered from SchoolM form.',
        ]);

        return $newId;
    }


    public static function discountForScholarship($totalFee, $scholarshipId): float
    {
        if (empty($scholarshipId)) {
            return 0;
        }

        $scholarship = DB::table('scholarship')->where('ScholarshipID', $scholarshipId)->first();
        return $scholarship ? self::money(((float) $totalFee * (float) $scholarship->DiscountPercentage) / 100) : 0;
    }

    public static function recalculateStudentFee(int $studentFeeId): void
    {
        $studentFee = DB::table('studentfee')->where('StudentFeeID', $studentFeeId)->first();
        if (!$studentFee) {
            return;
        }

        $totalAmount = self::money($studentFee->TotalAmount);
        $discount = self::money($studentFee->DiscountAmount);
        $paidAmount = self::money(DB::table('payment')->where('StudentFeeID', $studentFeeId)->sum('AmountPaid'));
        $fineAmount = self::money(DB::table('fine')->where('StudentFeeID', $studentFeeId)->sum('FineAmount'));
        $payable = max(self::money($totalAmount - $discount + $fineAmount), 0);
        $remainingBalance = self::money($payable - $paidAmount);

        DB::table('studentfee')->where('StudentFeeID', $studentFeeId)->update([
            'FineAmount' => $fineAmount,
            'RemainingBalance' => max($remainingBalance, 0),
            'Status' => $remainingBalance <= 0 ? 'Paid' : ($paidAmount > 0 ? 'Partially Paid' : 'Pending'),
        ]);
    }

    public static function assignFeeStructureToStudent(int $studentId, int $feeStructureId, ?string $dueDate = null): ?int
    {
        $student = DB::table('student')->where('StudentID', $studentId)->first();
        $feeStructure = DB::table('feestructure')->where('FeeStructureID', $feeStructureId)->first();

        if (!$student || !$feeStructure) {
            return null;
        }

        $discountAmount = self::discountForScholarship($feeStructure->TotalFee, $student->ScholarshipID);
        $existing = DB::table('studentfee')
            ->where('StudentID', $studentId)
            ->where('FeeStructureID', $feeStructureId)
            ->first();

        if ($existing) {
            $update = [
                'TotalAmount' => $feeStructure->TotalFee,
                'DiscountAmount' => $discountAmount,
            ];

            if (!empty($dueDate)) {
                $update['DueDate'] = $dueDate;
            }

            DB::table('studentfee')->where('StudentFeeID', $existing->StudentFeeID)->update($update);
            self::recalculateStudentFee((int) $existing->StudentFeeID);
            return (int) $existing->StudentFeeID;
        }

        $payableAmount = max(self::money($feeStructure->TotalFee - $discountAmount), 0);
        $studentFeeId = self::nextId('studentfee', 'StudentFeeID');

        DB::table('studentfee')->insert([
            'StudentFeeID' => $studentFeeId,
            'StudentID' => $studentId,
            'FeeStructureID' => $feeStructureId,
            'DueDate' => $dueDate,
            'TotalAmount' => $feeStructure->TotalFee,
            'DiscountAmount' => $discountAmount,
            'FineAmount' => 0,
            'RemainingBalance' => $payableAmount,
            'Status' => $payableAmount <= 0 ? 'Paid' : 'Pending',
        ]);

        return $studentFeeId;
    }

    public static function assignAllClassFeeStructuresToStudent(int $studentId, int $classId, ?string $dueDate = null): int
    {
        $feeStructures = DB::table('feestructure')
            ->where('ClassID', $classId)
            ->orderBy('TermID')
            ->get();

        $assigned = 0;
        foreach ($feeStructures as $feeStructure) {
            if (self::assignFeeStructureToStudent($studentId, (int) $feeStructure->FeeStructureID, $dueDate)) {
                $assigned++;
            }
        }

        return $assigned;
    }

    public static function studentBaseQuery()
    {
        return DB::table('student as s')
            ->leftJoin('parent as p', 's.ParentID', '=', 'p.ParentID')
            ->leftJoin('class as c', 's.ClassID', '=', 'c.ClassID')
            ->leftJoin('section as sec', 's.SectionID', '=', 'sec.SectionID')
            ->leftJoin('scholarship as sch', 's.ScholarshipID', '=', 'sch.ScholarshipID')
            ->select(
                's.StudentID', 's.First_Name', 's.Middle_Name', 's.Last_Name', 's.Gender',
                's.Date_of_Birth', 's.Contact_No', 's.Address', 's.Admission_Date',
                's.ClassID', 's.SectionID', 's.ParentID', 's.ScholarshipID',
                'c.ClassName', 'c.AcademicYear', 'sec.SectionName',
                'p.Father_Name', 'p.Mother_Name', 'p.Phone_No', 'p.Email',
                'sch.ScholarshipName', 'sch.DiscountPercentage'
            );
    }

    public static function studentProfileById(int $studentId)
    {
        return self::studentBaseQuery()->where('s.StudentID', $studentId)->first();
    }

    public static function searchStudents(?string $keyword = null, ?string $classId = null, int $limit = 50)
    {
        $query = self::studentBaseQuery();

        if (!empty($classId)) {
            $query->where('s.ClassID', $classId);
        }

        $keyword = self::normalizeSearch($keyword);
        $tokens = self::searchTokens($keyword);

        if ($keyword !== '') {
            $fullLike = '%' . $keyword . '%';
            $query->where(function ($main) use ($keyword, $fullLike, $tokens) {
                if (ctype_digit($keyword)) {
                    $main->orWhere('s.StudentID', (int) $keyword)
                        ->orWhere('s.ClassID', (int) $keyword)
                        ->orWhere('s.SectionID', (int) $keyword);
                }

                $main->orWhere('s.First_Name', 'LIKE', $fullLike)
                    ->orWhere('s.Middle_Name', 'LIKE', $fullLike)
                    ->orWhere('s.Last_Name', 'LIKE', $fullLike)
                    ->orWhere(DB::raw("TRIM(CONCAT_WS(' ', s.First_Name, s.Middle_Name, s.Last_Name))"), 'LIKE', $fullLike)
                    ->orWhere(DB::raw("TRIM(CONCAT_WS(' ', s.First_Name, s.Last_Name))"), 'LIKE', $fullLike)
                    ->orWhere('s.Contact_No', 'LIKE', $fullLike)
                    ->orWhere('s.Address', 'LIKE', $fullLike)
                    ->orWhere('p.Father_Name', 'LIKE', $fullLike)
                    ->orWhere('p.Mother_Name', 'LIKE', $fullLike)
                    ->orWhere('p.Phone_No', 'LIKE', $fullLike)
                    ->orWhere('p.Email', 'LIKE', $fullLike)
                    ->orWhere('c.ClassName', 'LIKE', $fullLike)
                    ->orWhere('c.AcademicYear', 'LIKE', $fullLike)
                    ->orWhere('sec.SectionName', 'LIKE', $fullLike);

                foreach ($tokens as $token) {
                    $like = '%' . $token . '%';
                    $main->orWhere(function ($q) use ($like) {
                        $q->whereRaw('LOWER(s.First_Name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(s.Middle_Name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(s.Last_Name) LIKE ?', [$like])
                            ->orWhereRaw("LOWER(TRIM(CONCAT_WS(' ', s.First_Name, s.Middle_Name, s.Last_Name))) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(TRIM(CONCAT_WS(' ', s.First_Name, s.Last_Name))) LIKE ?", [$like])
                            ->orWhereRaw('LOWER(s.Contact_No) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(s.Address) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(p.Father_Name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(p.Mother_Name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(p.Phone_No) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(p.Email) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(c.ClassName) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(c.AcademicYear) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(sec.SectionName) LIKE ?', [$like]);
                    });
                }
            });
        }

        return $query->orderByDesc('s.StudentID')->limit($limit)->get();
    }

    public static function feeRecordsForStudent(int $studentId)
    {
        $feeRecords = DB::table('studentfee as sf')
            ->leftJoin('feestructure as fs', 'sf.FeeStructureID', '=', 'fs.FeeStructureID')
            ->leftJoin('term as t', 'fs.TermID', '=', 't.TermID')
            ->leftJoin('class as c', 'fs.ClassID', '=', 'c.ClassID')
            ->select(
                'sf.StudentFeeID', 'sf.StudentID', 'sf.FeeStructureID', 'sf.DueDate',
                'sf.TotalAmount', 'sf.DiscountAmount', 'sf.FineAmount', 'sf.RemainingBalance', 'sf.Status',
                't.TermID', 't.TermName', 'c.ClassName',
                'fs.TuitionFee', 'fs.ExamFee', 'fs.TransportFee', 'fs.MiscFee', 'fs.TotalFee'
            )
            ->where('sf.StudentID', $studentId)
            ->orderByDesc('sf.StudentFeeID')
            ->get();

        foreach ($feeRecords as $fee) {
            $fee->payments = self::paymentsForStudentFee((int) $fee->StudentFeeID);
            $fee->fines = DB::table('fine')
                ->where('StudentFeeID', $fee->StudentFeeID)
                ->orderByDesc('AppliedDate')
                ->orderByDesc('FineID')
                ->get();

            $fee->TotalAmount = self::money($fee->TotalAmount ?? $fee->TotalFee);
            $fee->DiscountAmount = self::money($fee->DiscountAmount);
            $fee->FineAmount = self::money(DB::table('fine')->where('StudentFeeID', $fee->StudentFeeID)->sum('FineAmount'));
            $fee->PaidAmount = self::money(DB::table('payment')->where('StudentFeeID', $fee->StudentFeeID)->sum('AmountPaid'));
            $fee->PayableAmount = max(self::money($fee->TotalAmount - $fee->DiscountAmount + $fee->FineAmount), 0);
            $fee->RemainingBalance = max(self::money($fee->PayableAmount - $fee->PaidAmount), 0);
            $fee->OverPaidAmount = max(self::money($fee->PaidAmount - $fee->PayableAmount), 0);
            $fee->Status = $fee->RemainingBalance <= 0 ? 'Paid' : ($fee->PaidAmount > 0 ? 'Partially Paid' : 'Pending');
        }

        return $feeRecords;
    }

    public static function studentFeeSummary(int $studentId): object
    {
        $records = self::feeRecordsForStudent($studentId);

        return (object) [
            'record_count' => $records->count(),
            'total_fee' => self::money($records->sum('TotalAmount')),
            'total_discount' => self::money($records->sum('DiscountAmount')),
            'total_fine' => self::money($records->sum('FineAmount')),
            'total_payable' => self::money($records->sum('PayableAmount')),
            'total_paid' => self::money($records->sum('PaidAmount')),
            'total_due' => self::money($records->sum('RemainingBalance')),
            'total_overpaid' => self::money($records->sum('OverPaidAmount')),
        ];
    }


    public static function financeSummary(): object
    {
        $paymentSub = DB::table('payment')
            ->select('StudentFeeID', DB::raw('SUM(AmountPaid) as paid_amount'))
            ->groupBy('StudentFeeID');

        $fineSub = DB::table('fine')
            ->select('StudentFeeID', DB::raw('SUM(FineAmount) as fine_amount'))
            ->groupBy('StudentFeeID');

        $summary = DB::table('studentfee as sf')
            ->leftJoinSub($paymentSub, 'pay', function ($join) {
                $join->on('sf.StudentFeeID', '=', 'pay.StudentFeeID');
            })
            ->leftJoinSub($fineSub, 'fin', function ($join) {
                $join->on('sf.StudentFeeID', '=', 'fin.StudentFeeID');
            })
            ->select(
                DB::raw('COALESCE(SUM(sf.TotalAmount),0) as total_fee'),
                DB::raw('COALESCE(SUM(sf.DiscountAmount),0) as total_discount'),
                DB::raw('COALESCE(SUM(COALESCE(fin.fine_amount,0)),0) as total_fine'),
                DB::raw('COALESCE(SUM(COALESCE(pay.paid_amount,0)),0) as total_paid'),
                DB::raw('COALESCE(SUM(GREATEST((sf.TotalAmount - sf.DiscountAmount + COALESCE(fin.fine_amount,0)) - COALESCE(pay.paid_amount,0), 0)),0) as total_due')
            )
            ->first();

        $summary->total_payable = self::money($summary->total_fee - $summary->total_discount + $summary->total_fine);
        return $summary;
    }

    public static function classFinanceMetrics()
    {
        $paymentSub = DB::table('payment')
            ->select('StudentFeeID', DB::raw('SUM(AmountPaid) as paid_amount'))
            ->groupBy('StudentFeeID');

        $fineSub = DB::table('fine')
            ->select('StudentFeeID', DB::raw('SUM(FineAmount) as fine_amount'))
            ->groupBy('StudentFeeID');

        return DB::table('class as c')
            ->leftJoin('student as s', 'c.ClassID', '=', 's.ClassID')
            ->leftJoin('studentfee as sf', 's.StudentID', '=', 'sf.StudentID')
            ->leftJoinSub($paymentSub, 'pay', function ($join) {
                $join->on('sf.StudentFeeID', '=', 'pay.StudentFeeID');
            })
            ->leftJoinSub($fineSub, 'fin', function ($join) {
                $join->on('sf.StudentFeeID', '=', 'fin.StudentFeeID');
            })
            ->select(
                'c.ClassID', 'c.ClassName', 'c.AcademicYear',
                DB::raw('COUNT(DISTINCT s.StudentID) as total_students'),
                DB::raw('COALESCE(SUM(sf.TotalAmount),0) as total_fee'),
                DB::raw('COALESCE(SUM(sf.DiscountAmount),0) as total_discount'),
                DB::raw('COALESCE(SUM(COALESCE(fin.fine_amount,0)),0) as total_fine'),
                DB::raw('COALESCE(SUM((sf.TotalAmount - sf.DiscountAmount + COALESCE(fin.fine_amount,0))),0) as total_payable'),
                DB::raw('COALESCE(SUM(COALESCE(pay.paid_amount,0)),0) as total_paid'),
                DB::raw('COALESCE(SUM(GREATEST((sf.TotalAmount - sf.DiscountAmount + COALESCE(fin.fine_amount,0)) - COALESCE(pay.paid_amount,0), 0)),0) as total_dues')
            )
            ->groupBy('c.ClassID', 'c.ClassName', 'c.AcademicYear')
            ->orderBy('c.ClassName')
            ->get();
    }

    public static function paymentsForStudentFee(int $studentFeeId)
    {
        return DB::table('payment as p')
            ->leftJoin('receipt as r', 'p.PaymentID', '=', 'r.PaymentID')
            ->select(
                'p.PaymentID', 'p.StudentFeeID', 'p.PaymentDate', 'p.AmountPaid',
                'p.PaymentMethod', 'p.TransactionReference',
                'r.ReceiptID', 'r.ReceiptNumber', 'r.ReceiptDate'
            )
            ->where('p.StudentFeeID', $studentFeeId)
            ->orderByDesc('p.PaymentDate')
            ->orderByDesc('p.PaymentID')
            ->get();
    }

    public static function allPaymentsForStudent(int $studentId)
    {
        return DB::table('payment as p')
            ->join('studentfee as sf', 'p.StudentFeeID', '=', 'sf.StudentFeeID')
            ->leftJoin('receipt as r', 'p.PaymentID', '=', 'r.PaymentID')
            ->leftJoin('feestructure as fs', 'sf.FeeStructureID', '=', 'fs.FeeStructureID')
            ->leftJoin('term as t', 'fs.TermID', '=', 't.TermID')
            ->select(
                'p.PaymentID', 'p.StudentFeeID', 'p.PaymentDate', 'p.AmountPaid', 'p.PaymentMethod', 'p.TransactionReference',
                'r.ReceiptID', 'r.ReceiptNumber', 'r.ReceiptDate',
                't.TermName', 'sf.TotalAmount', 'sf.DiscountAmount', 'sf.FineAmount', 'sf.RemainingBalance', 'sf.Status'
            )
            ->where('sf.StudentID', $studentId)
            ->orderByDesc('p.PaymentDate')
            ->orderByDesc('p.PaymentID')
            ->get();
    }

    public static function makeReceiptNumber(int $paymentId, ?string $date = null): string
    {
        $datePart = $date ? date('Ymd', strtotime($date)) : date('Ymd');
        return 'RCPT-' . $datePart . '-' . str_pad((string) $paymentId, 5, '0', STR_PAD_LEFT);
    }

    public static function ensureReceiptForPayment(int $paymentId, ?string $paymentDate = null): object
    {
        $existing = DB::table('receipt')->where('PaymentID', $paymentId)->first();
        if ($existing) {
            return $existing;
        }

        $receiptNumber = self::makeReceiptNumber($paymentId, $paymentDate);
        if (DB::table('receipt')->where('ReceiptNumber', $receiptNumber)->exists()) {
            $receiptNumber .= '-' . Str::upper(Str::random(4));
        }

        $receiptId = self::nextId('receipt', 'ReceiptID');
        DB::table('receipt')->insert([
            'ReceiptID' => $receiptId,
            'PaymentID' => $paymentId,
            'ReceiptDate' => $paymentDate ?: date('Y-m-d'),
            'ReceiptNumber' => $receiptNumber,
        ]);

        return DB::table('receipt')->where('ReceiptID', $receiptId)->first();
    }

    public static function receiptDetails(int $paymentId)
    {
        $receipt = DB::table('payment as p')
            ->join('studentfee as sf', 'p.StudentFeeID', '=', 'sf.StudentFeeID')
            ->join('student as s', 'sf.StudentID', '=', 's.StudentID')
            ->leftJoin('parent as par', 's.ParentID', '=', 'par.ParentID')
            ->leftJoin('class as c', 's.ClassID', '=', 'c.ClassID')
            ->leftJoin('section as sec', 's.SectionID', '=', 'sec.SectionID')
            ->leftJoin('feestructure as fs', 'sf.FeeStructureID', '=', 'fs.FeeStructureID')
            ->leftJoin('term as t', 'fs.TermID', '=', 't.TermID')
            ->leftJoin('receipt as r', 'p.PaymentID', '=', 'r.PaymentID')
            ->select(
                'p.*', 'r.ReceiptID', 'r.ReceiptNumber', 'r.ReceiptDate',
                'sf.StudentFeeID', 'sf.TotalAmount', 'sf.DiscountAmount', 'sf.FineAmount', 'sf.RemainingBalance', 'sf.Status',
                's.StudentID', 's.First_Name', 's.Middle_Name', 's.Last_Name', 's.Contact_No', 's.Address',
                'par.Father_Name', 'par.Phone_No', 'c.ClassName', 'sec.SectionName', 't.TermName'
            )
            ->where('p.PaymentID', $paymentId)
            ->first();

        if (!$receipt) {
            return null;
        }

        if (empty($receipt->ReceiptNumber)) {
            $created = self::ensureReceiptForPayment($paymentId, $receipt->PaymentDate);
            $receipt->ReceiptID = $created->ReceiptID;
            $receipt->ReceiptNumber = $created->ReceiptNumber;
            $receipt->ReceiptDate = $created->ReceiptDate;
        }

        $fineTotal = self::money(DB::table('fine')->where('StudentFeeID', $receipt->StudentFeeID)->sum('FineAmount'));
        $totalAmount = self::money($receipt->TotalAmount);
        $discount = self::money($receipt->DiscountAmount);
        $payable = max(self::money($totalAmount - $discount + $fineTotal), 0);
        $paidBefore = self::money(DB::table('payment')
            ->where('StudentFeeID', $receipt->StudentFeeID)
            ->where('PaymentID', '<', $paymentId)
            ->sum('AmountPaid'));
        $paidUpToThisReceipt = self::money(DB::table('payment')
            ->where('StudentFeeID', $receipt->StudentFeeID)
            ->where('PaymentID', '<=', $paymentId)
            ->sum('AmountPaid'));
        $totalPaid = self::money(DB::table('payment')
            ->where('StudentFeeID', $receipt->StudentFeeID)
            ->sum('AmountPaid'));

        $receipt->FineAmount = $fineTotal;
        $receipt->TotalAmount = $totalAmount;
        $receipt->DiscountAmount = $discount;
        $receipt->NetPayable = $payable;
        $receipt->PaidBeforeThisReceipt = $paidBefore;
        $receipt->PaidUpToThisReceipt = $paidUpToThisReceipt;
        $receipt->TotalPaid = $totalPaid;
        $receipt->DuesAfterThisReceipt = max(self::money($payable - $paidUpToThisReceipt), 0);
        $receipt->CurrentDues = max(self::money($payable - $totalPaid), 0);
        $receipt->OverPaidAmount = max(self::money($totalPaid - $payable), 0);
        $receipt->Status = $receipt->CurrentDues <= 0 ? 'Paid' : ($totalPaid > 0 ? 'Partially Paid' : 'Pending');

        return $receipt;
    }
}
