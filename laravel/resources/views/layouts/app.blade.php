<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản lý thiết bị kho') · Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f4f6f9}.navbar-brand{font-weight:700}.card{border:0;box-shadow:0 .125rem .5rem #18223012}.table th{white-space:nowrap}
        .stat-card{border-left:4px solid #0d6efd}.table td{vertical-align:middle}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route(auth()->user()->landingRoute()) }}">Quản lý thiết bị kho</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            @auth
                @php($user = auth()->user())
                <ul class="navbar-nav me-auto">
                    @foreach([
                        ['dashboard.view','dashboard','Tổng quan'],
                        ['devices.view','devices.index','Thiết bị'],
                        ['tickets.view','tickets.index','Phiếu cấp phát'],
                        ['employees.view','employees.index','Nhân viên'],
                        ['departments.view','departments.index','Phòng ban'],
                        ['maintenance.view','maintenance.index','Bảo trì'],
                        ['reports.view','reports.index','Báo cáo'],
                        ['users.manage','users.index','Tài khoản'],
                        ['roles.manage','roles.index','Vai trò & quyền'],
                    ] as [$permission,$routeName,$label])
                        @if($user->hasPermission($permission))
                            <li class="nav-item"><a class="nav-link" href="{{ route($routeName) }}">{{ $label }}</a></li>
                        @endif
                    @endforeach
                </ul>
                <div class="d-flex align-items-center gap-3 text-white">
                    <a class="text-white text-decoration-none" href="{{ route('profile') }}">{{ $user->fullname }}</a>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-light">Đăng xuất</button></form>
                </div>
            @endauth
        </div>
    </div>
</nav>
<main class="container-fluid py-4">
    @if(session('success'))<div class="alert alert-success alert-dismissible">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger"><strong>Vui lòng kiểm tra thông tin:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
