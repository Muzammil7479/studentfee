@extends('layouts.admin')

@section('title','Dashboard')
@section('heading','Dashboard')

@section('content')
<div class="mb-4">
    <h5 class="mb-1">Welcome, {{ $user->name }} 👋</h5>
    <p class="text-muted mb-0">
        You are signed in as <span class="badge bg-primary text-uppercase">{{ $user->roleLabel() }}</span>
    </p>
</div>

<div class="row g-4">
    @foreach($cards as $card)
        <div class="col-md-4">
            <a href="{{ route($card[2]) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="btn btn-{{ $card[4] }} rounded-circle"><i class="fa {{ $card[3] }}"></i></span>
                            <h5 class="mb-0 text-dark">{{ $card[0] }}</h5>
                        </div>
                        <p class="text-muted small mb-0">{{ $card[1] }}</p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach

    @if($user->isAdmin())
        <div class="col-md-4">
            <a href="{{ route('admin.settings') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="btn btn-secondary rounded-circle"><i class="fa fa-gear"></i></span>
                            <h5 class="mb-0 text-dark">Settings</h5>
                        </div>
                        <p class="text-muted small mb-0">Configure application-wide settings</p>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>
@endsection
