@extends('layouts.admin')

@section('title','SchoolM Portal')
@section('heading','SchoolM Portal')

@section('content')
<div class="row g-4">
    @php
        $cards = [
            ['Account Section','Manage fee structures, payments, scholarships and receipts','account.dashboard','fa-wallet','primary'],
            ['Student View','Student profile, fee history and downloadable receipts','student.dashboard','fa-user-graduate','info'],
            ['Admin Dashboard','Admit students, classes, sections and student records','admin.dashboard','fa-user-shield','warning'],
            ['Teacher View','Teacher records and dynamic search','teachers.index','fa-chalkboard-user','success'],
            ['Principal Metrics','Class-wise admission strength and parent details','principal.dashboard','fa-user-tie','dark'],
        ];
    @endphp

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
</div>
@endsection
