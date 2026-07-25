@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h5 class="mb-3"><i class="fa-solid fa-gear me-2 text-primary"></i>System Settings</h5>
        <p class="text-muted">
            This is a starting point for application-wide settings (school name, logo, currency,
            academic session, etc.). Extend this page with the settings fields your project needs.
        </p>
        <div class="alert alert-info mb-0">
            Only administrators can view this page.
        </div>
    </div>
</div>
@endsection
