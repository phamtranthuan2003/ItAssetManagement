@extends('layouts.app')
@section('title','Bảo trì thiết bị')
@section('content')
<h1 class="h3 mb-3">Bảo trì và sửa chữa</h1>
@if(auth()->user()->hasPermission('maintenance.create'))<form method="post" action="{{ route('maintenance.store') }}" class="card card-body mb-3">@csrf<h2 class="h5">Tạo phiếu bảo trì</h2><div class="row g-2">
<div class="col-md-3"><label class="form-label">Thiết bị</label><select class="form-select" name="device_id" required>@foreach($devices as $device)<option value="{{ $device->id }}">{{ $device->device_code }} · {{ $device->brand }} {{ $device->model }}</option>@endforeach</select></div>
<div class="col-md-2"><label class="form-label">Loại</label><select class="form-select" name="maintenance_type">@foreach(['repair'=>'Sửa chữa','upgrade'=>'Nâng cấp','routine'=>'Định kỳ'] as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
<div class="col-md-2"><label class="form-label">Ngày bắt đầu</label><input type="date" class="form-control" name="start_date" value="{{ now()->toDateString() }}" required></div>
<div class="col-md-2"><label class="form-label">Ngày kết thúc</label><input type="date" class="form-control" name="end_date"></div>
<div class="col-md-3"><label class="form-label">Đơn vị thực hiện</label><input class="form-control" name="provider"></div>
<div class="col-md-2"><label class="form-label">Chi phí</label><input type="number" step="0.01" min="0" class="form-control" name="cost"></div>
<div class="col-md-3"><label class="form-label">Trạng thái</label><select class="form-select" name="status"><option value="in_progress">Đang xử lý</option><option value="completed">Hoàn thành</option><option value="cannot_repair">Không thể sửa</option></select></div>
<div class="col-md-7"><label class="form-label">Mô tả sự cố / nội dung</label><input class="form-control" name="description" required></div><div class="col-12"><button class="btn btn-primary">Lưu phiếu bảo trì</button></div></div></form>@endif
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Ngày</th><th>Thiết bị</th><th>Loại</th><th>Mô tả</th><th>Chi phí</th><th>Trạng thái</th><th></th></tr></thead><tbody>
@forelse($records as $record)<tr><td>{{ $record->start_date }}</td><td>{{ $record->device_code }} · {{ $record->brand }} {{ $record->model }}</td><td>{{ $record->maintenance_type }}</td><td>{{ $record->description }}</td><td>{{ number_format((float)$record->cost) }}</td><td>{{ ['in_progress'=>'Đang xử lý','completed'=>'Hoàn thành','cannot_repair'=>'Không thể sửa'][$record->status] ?? $record->status }}</td><td>@if(auth()->user()->hasPermission('maintenance.update'))<form method="post" action="{{ route('maintenance.update',$record->id) }}" class="d-flex gap-1">@csrf @method('PUT')<select name="status" class="form-select form-select-sm"><option value="in_progress">Đang xử lý</option><option value="completed">Hoàn thành</option><option value="cannot_repair">Không thể sửa</option></select><button class="btn btn-sm btn-outline-primary">Cập nhật</button></form>@endif</td></tr>@empty<tr><td colspan="7" class="text-center py-4">Chưa có phiếu bảo trì.</td></tr>@endforelse
</tbody></table></div><div class="card-body">{{ $records->links() }}</div></div>
@endsection
