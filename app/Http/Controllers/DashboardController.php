<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the role-aware dashboard.
     *
     * Both admin and user roles land here after login (per spec item 9);
     * the view itself decides what to render based on the role.
     */
    public function index(): View
    {
        $user = Auth::user();

        $adminCards = [
            ['Student Management', 'Admit students, manage classes, sections and records', 'admin.dashboard', 'fa-user-shield', 'warning'],
            ['Teacher Management', 'Teacher records and directory', 'teachers.index', 'fa-chalkboard-user', 'success'],
            ['Fee & Accounts', 'Fee structures, payments, scholarships and receipts', 'account.dashboard', 'fa-wallet', 'primary'],
            ['Reports', 'Class-wise strength, fee and payment reports', 'principal.dashboard', 'fa-chart-line', 'dark'],
            ['Student View', 'Student profile, fee history and receipts', 'student.dashboard', 'fa-user-graduate', 'info'],
            ['User Management', 'Create, edit and manage system users', 'admin.users.index', 'fa-users-gear', 'danger'],
        ];

        $userCards = [
            ['View Students', 'Browse student profiles and fee history', 'student.dashboard', 'fa-user-graduate', 'info'],
            ['Add Fee Payments', 'Record fee payments and print receipts', 'account.dashboard', 'fa-wallet', 'primary'],
            ['Reports', 'Class-wise strength and fee reports', 'principal.dashboard', 'fa-chart-line', 'dark'],
        ];

        $cards = $user->isAdmin() ? $adminCards : $userCards;

        return view('dashboard', compact('cards', 'user'));
    }
}
