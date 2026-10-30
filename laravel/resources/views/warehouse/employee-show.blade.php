@extends('layouts.app')
@section('title','Hồ sơ nhân viên')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="h3 mb-1">Hồ sơ nhân viên: {{ $employee->fullname }}</h1><div class="text-secondary">{{ $employee->code }} · {{ $employee->department_name }}</div></div><a class="btn btn-outline-secondary" href="{{ route('employees.index',request()->query()) }}">← Danh sách nhân viên</a></div>
<div class="row g-4">
<div class="col-lg-4"><section class="card mb-4"><div class="card-header bg-white d-flex justify-content-between"><strong>Hồ sơ nhân viên</strong><span class="badge text-bg-{{ $employee->status==='working'?'success':'secondary' }}">{{ $employee->status==='working'?'Đang làm việc':'Đã nghỉ việc' }}</span></div><div class="card-body">
@if(auth()->user()->hasPermission('employees.update'))
<form method="post" action="{{ route('employees.update',$employee->id) }}">@csrf @method('PUT')
<div class="mb-3"><label class="form-label">STT</label><input class="form-control" value="{{ $employee->id }}" readonly></div>
<div class="mb-3"><label class="form-label">Mã nhân viên</label><input class="form-control" name="code" value="{{ $employee->code }}" required></div>
<div class="mb-3"><label class="form-label">Họ và tên</label><input class="form-control" name="fullname" value="{{ $employee->fullname }}" required></div>
<div class="mb-3"><label class="form-label">Phòng ban</label><select name="department_id" class="form-select" required>@foreach($departments as $department)<option value="{{ $department->id }}" @selected($department->id===$employee->department_id)>{{ $department->name }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label">Chức vụ</label><input class="form-control" name="position" value="{{ $employee->position }}"></div>
<div class="mb-3"><label class="form-label">Số điện thoại</label><input class="form-control" name="phone" value="{{ $employee->phone }}"></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="{{ $employee->email }}"></div>
<div class="mb-3"><label class="form-label">Tình trạng nhân sự</label><select name="status" class="form-select"><option value="working" @selected($employee->status==='working')>Đang làm việc</option><option value="resigned" @selected($employee->status==='resigned')>Đã nghỉ việc</option></select></div>
<button class="btn btn-primary">Lưu thông tin nhân viên</button></form>
@else
<dl class="row mb-0"><dt class="col-5">Họ và tên</dt><dd class="col-7">{{ $employee->fullname }}</dd><dt class="col-5">Phòng ban</dt><dd class="col-7">{{ $employee->department_name }}</dd><dt class="col-5">Chức vụ</dt><dd class="col-7">{{ $employee->position ?: '—' }}</dd><dt class="col-5">Điện thoại</dt><dd class="col-7">{{ $employee->phone ?: '—' }}</dd><dt class="col-5">Email</dt><dd class="col-7">{{ $employee->email ?: '—' }}</dd></dl>
@endif
</div></section></div>
<div class="col-lg-8">
@if($heldDevices->isNotEmpty())
<div class="alert alert-{{ $employee->status==='resigned'?'danger':'info' }} d-flex justify-content-between align-items-center"><div><strong>{{ $employee->status==='resigned'?'Nhân viên đã nghỉ việc nhưng vẫn đang giữ thiết bị!':'Thiết bị đang nắm giữ' }}</strong><div>{{ $heldDevices->count() }} thiết bị đang được cấp phát.</div></div>
@if(auth()->user()->hasPermission('employees.revoke'))<button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bulkRevokeModal">Thu hồi tất cả về kho</button>@endif</div>
@else<h2 class="h5 mb-3">Thiết bị đang nắm giữ</h2>@endif
<section class="card mb-4"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>STT</th><th>Loại & model</th><th>Cấu hình</th><th>Hình thức</th><th>Thời hạn</th><th></th></tr></thead><tbody>
@forelse($heldDevices as $device)<tr><td><a href="{{ route('devices.show',$device->id) }}">{{ $device->id }}</a></td><td><span class="badge text-bg-secondary">{{ $device->type }}</span> <strong>{{ $device->brand }} {{ $device->model }}</strong></td><td><small>{{ $device->cpu ?: '—' }}</small><br><small>RAM {{ $device->ram_gb ?: '—' }}GB | SSD {{ $device->ssd_capacity ?: '—' }}</small></td><td>{{ $device->is_temporary?'Mượn tạm':'Dài hạn' }}</td><td>{{ $device->is_temporary?$device->expected_return_date:'—' }}</td><td><a class="btn btn-sm btn-outline-secondary" href="{{ route('devices.show',$device->id) }}">Chi tiết</a></td></tr>
@empty<tr><td colspan="6" class="text-center py-4">Nhân viên hiện không nắm giữ thiết bị nào.</td></tr>@endforelse
</tbody></table></div></section>
<div class="d-flex justify-content-end">@if($employee->status==='working' && auth()->user()->hasPermission('tickets.create'))<a class="btn btn-success" href="{{ route('tickets.create',['type'=>'allocation','to_employee_id'=>$employee->id,'to_department_id'=>$employee->department_id]) }}">Lập phiếu cấp phát mới cho nhân viên này</a>@endif</div>
</div></div>
@if($heldDevices->isNotEmpty() && auth()->user()->hasPermission('employees.revoke'))
<div class="modal fade" id="bulkRevokeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="post" action="{{ route('employees.bulk-revoke',$employee->id) }}">@csrf
<div class="modal-header bg-danger text-white"><h2 class="modal-title fs-5">Thu hồi tất cả thiết bị</h2><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><p>Hệ thống tạo phiếu thu hồi cho {{ $heldDevices->count() }} thiết bị:</p><ul class="list-group mb-3">@foreach($heldDevices as $device)<li class="list-group-item">STT {{ $device->id }} · {{ $device->brand }} {{ $device->model }} ({{ $device->type }})</li>@endforeach</ul>
<div class="mb-3"><label class="form-label">Tình trạng khi thu nhận</label><select name="returned_condition" class="form-select" required><option value="good">Tốt — trả về kho</option><option value="broken">Hỏng — chuyển bảo trì</option><option value="missing_accessories">Thiếu phụ kiện — trả về kho</option></select></div>
<div class="mb-3"><label class="form-label">Lý do thu hồi</label><input class="form-control" name="reason" value="Thu hồi thiết bị khi nhân viên bàn giao/nghỉ việc"></div>
<div class="mb-3"><label class="form-label">Ghi chú kiểm kê phụ kiện</label><textarea class="form-control" name="notes" rows="2"></textarea></div>
@if($employee->status==='working')<div class="form-check p-3 bg-light rounded border"><input class="form-check-input" type="checkbox" name="mark_as_resigned" value="1" id="markResigned"><label class="form-check-label text-danger" for="markResigned">Đồng thời cập nhật nhân viên thành “Đã nghỉ việc”</label></div>@endif
</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button><button class="btn btn-danger">Xác nhận thu hồi</button></div>
</form></div></div></div>
@endif
@endsection
