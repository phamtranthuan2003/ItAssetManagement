@extends('layouts.app')
@section('title','Vai trò và quyền')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="h3 mb-1">Quản lý vai trò & quyền</h1><p class="text-secondary mb-0">Quyền được áp dụng trực tiếp từ role_permissions; chỉ tài khoản ID 6 có quyền bỏ qua RBAC.</p></div></div>
<form method="post" action="{{ route('roles.store') }}" class="card card-body mb-3">@csrf<h2 class="h5">Tạo vai trò</h2><div class="row g-2"><div class="col-md-3"><label class="form-label">Mã vai trò</label><input class="form-control" name="code" pattern="[a-z][a-z0-9_]{1,39}" required></div><div class="col-md-5"><label class="form-label">Tên vai trò</label><input class="form-control" name="name" required></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Tạo vai trò</button></div></div></form>
<div class="row g-3">
@foreach($roles as $role)<div class="col-xl-6"><section class="card h-100"><div class="card-header bg-white d-flex justify-content-between"><strong>{{ $role->name }}</strong><code>{{ $role->code }}</code></div>
<form method="post" action="{{ route('roles.permissions',$role->id) }}" class="card-body">@csrf @method('PUT')<div class="row">
@foreach($catalog as $code=>$label)<div class="col-md-6"><label class="form-check mb-2"><input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $code }}" @checked(in_array($code,$role->permissions,true))> <span class="form-check-label">{{ $label }}</span></label></div>@endforeach
</div><button class="btn btn-sm btn-primary mt-2">Lưu quyền</button></form>
@if(!in_array($role->code,['admin','warehouse','customer'],true))<div class="card-footer bg-white"><form method="post" action="{{ route('roles.destroy',$role->id) }}" onsubmit="return confirm('Xóa vai trò này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa vai trò</button></form></div>@endif
</section></div>@endforeach
</div>
@endsection
