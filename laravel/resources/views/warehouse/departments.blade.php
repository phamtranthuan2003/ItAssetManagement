@extends('layouts.app')
@section('title','Phòng ban')
@section('content')
<h1 class="h3 mb-3">Quản lý phòng ban</h1>
@if(auth()->user()->hasPermission('departments.create'))<form method="post" action="{{ route('departments.store') }}" class="card card-body mb-3">@csrf<div class="row g-2 align-items-end"><div class="col-md-2"><label class="form-label">Mã phòng ban</label><input class="form-control" name="code" required></div><div class="col-md-3"><label class="form-label">Tên phòng ban</label><input class="form-control" name="name" required></div><div class="col-md-5"><label class="form-label">Mô tả</label><input class="form-control" name="description"></div><div class="col-md-2"><button class="btn btn-primary w-100">Thêm phòng ban</button></div></div></form>@endif
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Mã</th><th>Tên phòng ban</th><th>Mô tả</th><th></th></tr></thead><tbody>
@forelse($departments as $department)<tr><td>{{ $department->code }}</td><td>{{ $department->name }}</td><td>{{ $department->description }}</td><td class="text-end">@if(auth()->user()->hasPermission('departments.update'))<details><summary class="btn btn-sm btn-outline-secondary">Sửa</summary><form method="post" action="{{ route('departments.update',$department->id) }}" class="d-flex gap-1 mt-2">@csrf @method('PUT')<input class="form-control form-control-sm" name="code" value="{{ $department->code }}" required><input class="form-control form-control-sm" name="name" value="{{ $department->name }}" required><input class="form-control form-control-sm" name="description" value="{{ $department->description }}"><button class="btn btn-sm btn-primary">Lưu</button></form></details>@endif
@if(auth()->user()->hasPermission('departments.delete'))<form method="post" action="{{ route('departments.destroy',$department->id) }}" class="d-inline" onsubmit="return confirm('Xóa phòng ban này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button></form>@endif</td></tr>
@empty<tr><td colspan="4" class="text-center py-4">Chưa có phòng ban.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
