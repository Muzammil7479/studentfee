<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SchoolM')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body{background:#f4f6f9;}
        .sidebar{width:250px;min-height:100vh;background:#1b263b;position:fixed;color:white;}
        .sidebar h3{padding:18px;text-align:center;border-bottom:1px solid rgba(255,255,255,.15);}
        .sidebar a{color:white;display:block;padding:13px 18px;text-decoration:none;}
        .sidebar a:hover,.sidebar a.active{background:#415a77;}
        .main{margin-left:250px;}
        .topbar{background:white;padding:14px 22px;box-shadow:0 2px 5px rgba(0,0,0,.12);display:flex;justify-content:space-between;align-items:center;}
        .content{padding:24px;}
        .card-box{border-radius:14px;color:white;padding:20px;margin-bottom:20px;}
        .blue{background:#0d6efd}.green{background:#198754}.orange{background:#fd7e14}.red{background:#dc3545}.purple{background:#6f42c1}
        .table td,.table th{vertical-align:middle;}
        @media(max-width: 768px){.sidebar{position:relative;width:100%;min-height:auto}.main{margin-left:0}.sidebar a{display:inline-block}}
        @media print{.sidebar,.topbar,.no-print{display:none!important}.main{margin-left:0}.content{padding:0}body{background:white}}
    </style>
</head>
<body>
<div class="sidebar no-print">
    <h3><i class="fa fa-school me-2"></i>SchoolM</h3>
    <a href="{{ route('dashboard') }}"><i class="fa fa-gauge-high me-2"></i>Dashboard</a>

    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-user-shield me-2"></i>Student Management</a>
            <a href="{{ route('teachers.index') }}"><i class="fa fa-chalkboard-user me-2"></i>Teacher</a>
            <a href="{{ route('account.dashboard') }}"><i class="fa fa-wallet me-2"></i>Accounts</a>
            <a href="{{ route('principal.dashboard') }}"><i class="fa fa-user-tie me-2"></i>Reports</a>
            <a href="{{ route('student.dashboard') }}"><i class="fa fa-user-graduate me-2"></i>Student View</a>
            <a href="{{ route('admin.users.index') }}"><i class="fa fa-users-gear me-2"></i>User Management</a>
            <a href="{{ route('admin.settings') }}"><i class="fa fa-gear me-2"></i>Settings</a>
        @else
            <a href="{{ route('student.dashboard') }}"><i class="fa fa-user-graduate me-2"></i>View Students</a>
            <a href="{{ route('account.dashboard') }}"><i class="fa fa-wallet me-2"></i>Add Fee Payments</a>
            <a href="{{ route('principal.dashboard') }}"><i class="fa fa-user-tie me-2"></i>Reports</a>
        @endif
    @endauth
</div>

<div class="main">
    <div class="topbar no-print">
        <div>
            <h4 class="mb-0">@yield('heading', 'SchoolM Dashboard')</h4>
            <small class="text-muted">Student Fee Management System</small>
        </div>

        @auth
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-inline-flex align-items-center justify-content-center"
                          style="width:32px;height:32px;border-radius:50%;background:#2563EB;color:#fff;font-size:14px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <span class="badge bg-secondary text-uppercase d-none d-md-inline">{{ auth()->user()->roleLabel() }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fa fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('password.edit') }}"><i class="fa fa-key me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fa fa-right-from-bracket me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <span class="badge bg-success">Live Project</span>
        @endauth
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success no-print">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger no-print">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger no-print">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    document.querySelectorAll('form.js-live-search').forEach(function (form) {
        let timer = null;
        const textInput = form.querySelector('input[type="text"]');
        if (!textInput) return;
        textInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const value = textInput.value.trim();
                if (value.length === 0 || value.length >= 2) {
                    form.submit();
                }
            }, 700);
        });
    });
})();
</script>

@yield('scripts')
</body>
</html>
