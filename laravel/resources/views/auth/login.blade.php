<!doctype html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đăng nhập · Quản lý thiết bị kho</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{min-height:100vh;background:linear-gradient(140deg,#eaf2ff,#f7f8fb);display:grid;place-items:center}.card{width:min(440px,92vw);border:0;box-shadow:0 1rem 3rem #14213d18}</style></head>
<body><main class="card p-4 p-md-5"><h1 class="h4 mb-1">Quản lý thiết bị kho</h1><p class="text-secondary mb-4">Đăng nhập hệ thống</p>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="post" action="{{ route('login.authenticate') }}">@csrf
        <div class="mb-3"><label class="form-label" for="username">Tên đăng nhập</label><input id="username" name="username" class="form-control" value="{{ old('username') }}" required autofocus></div>
        <div class="mb-3"><label class="form-label" for="password">Mật khẩu</label><input id="password" type="password" name="password" class="form-control" required></div>
        <button class="btn btn-primary w-100">Đăng nhập</button>
    </form>
</main></body></html>
