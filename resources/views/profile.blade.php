@extends('layouts.admin')

@section('title', 'My Profile')
@section('heading', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width:84px;height:84px;border-radius:50%;background:#2563EB;color:#fff;font-size:32px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="mb-0">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                <span class="badge bg-primary text-uppercase">{{ $user->roleLabel() }}</span>

                <hr class="my-4">

                <a href="{{ route('password.edit') }}" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-key me-2"></i>Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
