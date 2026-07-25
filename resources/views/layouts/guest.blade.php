<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login - School Fee Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root{
            --brand-primary:#2563EB;
            --brand-secondary:#1E40AF;
            --brand-bg:#F8FAFC;
        }
        html,body{height:100%;}
        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg, var(--brand-secondary) 0%, var(--brand-primary) 55%, #3B82F6 100%);
            font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
            padding:24px;
        }
        .auth-card{
            width:100%;
            max-width:420px;
            background:#ffffff;
            border-radius:18px;
            box-shadow:0 25px 60px rgba(15,23,42,.35);
            overflow:hidden;
        }
        .auth-card .card-top{
            padding:36px 32px 8px;
            text-align:center;
        }
        .auth-logo{
            width:72px;
            height:72px;
            border-radius:50%;
            background:linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 16px;
            box-shadow:0 10px 25px rgba(37,99,235,.35);
        }
        .auth-logo i{
            font-size:32px;
            color:#fff;
        }
        .auth-card h1{
            font-size:1.35rem;
            font-weight:700;
            color:#0f172a;
            margin-bottom:2px;
        }
        .auth-card p.subtitle{
            color:#64748b;
            font-size:.9rem;
            margin-bottom:0;
        }
        .auth-card .card-body{
            padding:28px 32px 36px;
        }
        .form-label{
            font-weight:600;
            font-size:.85rem;
            color:#334155;
        }
        .form-control{
            padding:.65rem .9rem;
            border-radius:10px;
            border:1px solid #e2e8f0;
            background:var(--brand-bg);
        }
        .form-control:focus{
            border-color:var(--brand-primary);
            box-shadow:0 0 0 .2rem rgba(37,99,235,.15);
            background:#fff;
        }
        .input-group .btn-toggle-password{
            border:1px solid #e2e8f0;
            border-left:none;
            background:var(--brand-bg);
            color:#64748b;
        }
        .input-group .btn-toggle-password:hover{
            color:var(--brand-primary);
        }
        .btn-login{
            background:linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            border:none;
            padding:.7rem;
            font-weight:600;
            border-radius:10px;
            letter-spacing:.3px;
        }
        .btn-login:hover, .btn-login:focus{
            background:linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
            color:#fff;
        }
        .form-check-label{
            font-size:.88rem;
            color:#475569;
        }
        .alert{
            border-radius:10px;
            font-size:.88rem;
        }
        .demo-hint{
            font-size:.78rem;
            color:#94a3b8;
            text-align:center;
            margin-top:18px;
        }
        @media (max-width: 420px){
            .auth-card .card-top{padding:28px 20px 6px;}
            .auth-card .card-body{padding:22px 20px 28px;}
        }
    </style>
</head>
<body>

    @yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
