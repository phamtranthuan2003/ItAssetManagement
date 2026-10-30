@extends('layouts.app')
@section('title','Quản lý tài khoản')
@section('content')
<h1 class="h3 mb-3">Quản lý tài khoản hệ thống</h1>
<form method="post" action="{{ route('users.store') }}" class="card card-body mb-3">@csrf<h2 class="h5">Tạo tài khoản</h2><div class="row g-2 align-items-end">
<div class="col-md-2"><label class="form-label">Tên đăng nhập</label><input class="form-control" name="username" required maxlength="50"></div><div class="col-md-2"><label class="form-label">Mật khẩu</label><input type="password" class="form-control" name="password" required minlength="6"></div><div class="col-md-2"><label class="form-label">Họ tên</label><input class="form-control" name="fullname" required></div><div class="col-md-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email"></div><div class="col-md-2"><label class="form-label">Vai trò</label><select class="form-select" name="role">@foreach($roles as $role)<option value="{{ $role->code }}">{{ $role->name }}</option>@endforeach</select></div><div class="col-md-1"><label class="form-label">Trạng thái</label><select name="status" class="form-select"><option value="1">Hoạt động</option><option value="0">Khóa</option></select></div><div class="col-md-1"><button class="btn btn-primary w-100">Tạo</button></div></div></form>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>ID</th><th>Tên đăng nhập</th><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
@foreach($users as $account)
@php($isSelf=(int)$account->id===(int)auth()->id())
<tr><td>{{ $account->id }} @if((int)$account->id===6)<span class="badge text-bg-warning">Super Admin</span>@endif</td><td>{{ $account->username }}</td><td>{{ $account->fullname }}</td><td>{{ $account->email }}</td><td>{{ $account->role }}</td><td>{{ $account->status?'Hoạt động':'Đã khóa' }}</td><td>
@if(((int)$account->id!==6 || auth()->user()->isSuperAdmin()) && ($account->role!=='admin' || auth()->user()->isSuperAdmin()))
<details><summary class="btn btn-sm btn-outline-primary">Sửa</summary><form method="post" action="{{ route('users.update',$account->id) }}" class="bg-light p-2 mt-1">@csrf @method('PUT')
<div class="row g-1"><div class="col"><label class="small">Họ tên</label><input class="form-control form-control-sm" name="fullname" value="{{ $account->fullname }}" required></div><div class="col"><label class="small">Email</label><input type="email" class="form-control form-control-sm" name="email" value="{{ $account->email }}"></div>
@if($isSelf || (int)$account->id===6)<input type="hidden" name="role" value="{{ $account->role }}"><input type="hidden" name="status" value="{{ $account->status }}">@else
<div class="col"><label class="small">Vai trò</label><select class="form-select form-select-sm" name="role">@foreach($roles as $role)<option value="{{ $role->code }}" @selected($role->code===$account->role)>{{ $role->name }}</option>@endforeach</select></div><div class="col"><label class="small">Trạng thái</label><select class="form-select form-select-sm" name="status"><option value="1" @selected($account->status)>Hoạt động</option><option value="0" @selected(!$account->status)>Khóa</option></select></div>@endif
<div class="col"><label class="small">Mật khẩu mới (không bắt buộc)</label><input type="password" class="form-control form-control-sm" name="password" minlength="6"></div><div class="col-12"><button class="btn btn-sm btn-primary mt-1">Lưu</button></div></div></form></details>
@endif
@if((int)$account->id!==6 && !$isSelf && ($account->role!=='admin' || auth()->user()->isSuperAdmin()))<form method="post" action="{{ route('users.destroy',$account->id) }}" class="d-inline" onsubmit="return confirm('Vô hiệu hóa tài khoản này? Lịch sử sẽ được giữ lại.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Vô hiệu hóa</button></form>@endif
</td></tr>
@endforeach
</tbody></table></div></div>
@endsection
