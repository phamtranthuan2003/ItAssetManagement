@extends('layouts.app')
@section('title','Thông tin tài khoản')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><h1 class="h3 mb-3">Thông tin tài khoản</h1><div class="card"><div class="card-body">
<dl class="row"><dt class="col-sm-4">Tên đăng nhập</dt><dd class="col-sm-8">{{ auth()->user()->username }}</dd><dt class="col-sm-4">Họ tên</dt><dd class="col-sm-8">{{ auth()->user()->fullname }}</dd><dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ auth()->user()->email ?: '—' }}</dd><dt class="col-sm-4">Vai trò</dt><dd class="col-sm-8">{{ auth()->user()->role }}</dd></dl>
<hr><h2 class="h5">Đổi mật khẩu</h2><form method="post" action="{{ route('profile.password') }}">@csrf
<div class="mb-3"><label class="form-label">Mật khẩu hiện tại</label><input type="password" name="current_password" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Mật khẩu mới (ít nhất 6 ký tự)</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
<div class="mb-3"><label class="form-label">Xác nhận mật khẩu mới</label><input type="password" name="new_password_confirmation" class="form-control" required></div>
<button class="btn btn-primary">Cập nhật mật khẩu</button></form></div></div></div></div>
@endsection
