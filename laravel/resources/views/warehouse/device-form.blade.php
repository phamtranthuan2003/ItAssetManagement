@extends('layouts.app')
@section('title',$device?'Sửa thiết bị':'Nhập thiết bị')
@section('content')
<div class="mb-3"><h1 class="h3">{{ $device?'Cập nhật thiết bị':'Nhập thiết bị mới' }}</h1><p class="text-secondary">Thông tin lưu theo cấu trúc bảng thiết bị và thông số kỹ thuật hiện có.</p></div>
<form method="post" action="{{ $device?route('devices.update',$device->id):route('devices.store') }}" class="card card-body">
@csrf @if($device)@method('PUT')@endif
<div class="row g-3">
@foreach(['device_code'=>'Mã thiết bị','type'=>'Loại thiết bị','brand'=>'Hãng sản xuất','model'=>'Model','serial_number'=>'Số serial','import_date'=>'Ngày nhập kho','warranty_expiry'=>'Hạn bảo hành'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$device->$field ?? ($field==='import_date'?now()->toDateString():'')) }}" @if(in_array($field,['import_date'])) type="date" @endif @if(in_array($field,['device_code','type','brand','model','import_date'])) required @endif></div>
@endforeach
<div class="col-md-6"><label class="form-label">Tình trạng</label><select name="condition_state" class="form-select">@foreach(['new'=>'Mới','used'=>'Đang sử dụng','broken'=>'Hỏng','under_warranty'=>'Bảo hành'] as $key=>$label)<option value="{{ $key }}" @selected(old('condition_state',$device->condition_state ?? 'new')===$key)>{{ $label }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Ghi chú</label><textarea class="form-control" name="notes">{{ old('notes',$device->notes ?? '') }}</textarea></div>
<div class="col-12"><h2 class="h5 mt-2">Thông số kỹ thuật</h2></div>
@foreach(['cpu'=>'CPU','mainboard'=>'Mainboard','ram_gb'=>'RAM (GB)','ram_type'=>'Loại RAM','ssd_capacity'=>'SSD','hdd_capacity'=>'HDD','gpu'=>'Card đồ họa','screen_size'=>'Kích thước màn hình'] as $field=>$label)
<div class="col-md-3"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$spec->$field ?? '') }}" @if($field==='ram_gb') type="number" min="0" @endif></div>
@endforeach
<div class="col-12"><label class="form-label">Ghi chú thông số</label><textarea class="form-control" name="spec_notes">{{ old('spec_notes',$spec->notes ?? '') }}</textarea></div>
<div class="col-12"><hr><div class="form-check"><input class="form-check-input" type="checkbox" name="has_monitors" value="1" id="hasMonitors" @checked(old('has_monitors',count($monitors)>0))><label class="form-check-label fw-semibold" for="hasMonitors">Có kèm màn hình rời</label></div></div>
<div class="col-12" id="monitorFields"><div id="monitorRows">
@foreach(old('monitors',$monitors) as $index=>$monitor)<div class="monitor-row row g-2 align-items-end border rounded p-2 mb-2"><div class="col-md-4"><label class="form-label">Hãng màn hình</label><input class="form-control" data-field="brand" name="monitors[{{ $index }}][brand]" value="{{ data_get($monitor,'brand') }}" required></div><div class="col-md-4"><label class="form-label">Model</label><input class="form-control" data-field="model" name="monitors[{{ $index }}][model]" value="{{ data_get($monitor,'model') }}"></div><div class="col-md-2"><label class="form-label">Kích thước (inch)</label><input class="form-control" type="number" step="0.1" min="1" max="100" data-field="size" name="monitors[{{ $index }}][size]" value="{{ data_get($monitor,'size') }}"></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-monitor w-100">Xóa màn hình</button></div></div>@endforeach
</div><button type="button" id="addMonitor" class="btn btn-sm btn-outline-primary">+ Thêm màn hình</button></div>
</div><div class="mt-4"><button class="btn btn-primary">Lưu thiết bị</button> <a class="btn btn-outline-secondary" href="{{ route('devices.index') }}">Hủy</a></div>
</form>
<template id="monitorTemplate"><div class="monitor-row row g-2 align-items-end border rounded p-2 mb-2"><div class="col-md-4"><label class="form-label">Hãng màn hình</label><input class="form-control" data-field="brand" required></div><div class="col-md-4"><label class="form-label">Model</label><input class="form-control" data-field="model"></div><div class="col-md-2"><label class="form-label">Kích thước (inch)</label><input class="form-control" type="number" step="0.1" min="1" max="100" data-field="size"></div><div class="col-md-2"><button type="button" class="btn btn-outline-danger remove-monitor w-100">Xóa màn hình</button></div></div></template>
<script>
const monitorToggle=document.getElementById('hasMonitors'),monitorFields=document.getElementById('monitorFields'),monitorRows=document.getElementById('monitorRows'),monitorTemplate=document.getElementById('monitorTemplate');
function syncMonitors(){monitorFields.classList.toggle('d-none',!monitorToggle.checked);monitorRows.querySelectorAll('input').forEach(input=>input.disabled=!monitorToggle.checked);if(monitorToggle.checked&&!monitorRows.children.length)addMonitorRow()}
function addMonitorRow(){const row=monitorTemplate.content.firstElementChild.cloneNode(true);monitorRows.appendChild(row);syncMonitorNames()}
function syncMonitorNames(){monitorRows.querySelectorAll('.monitor-row').forEach((row,index)=>row.querySelectorAll('[data-field]').forEach(input=>input.name=`monitors[${index}][${input.dataset.field}]`))}
document.getElementById('addMonitor').addEventListener('click',addMonitorRow);monitorToggle.addEventListener('change',syncMonitors);monitorRows.addEventListener('click',event=>{if(event.target.classList.contains('remove-monitor')){event.target.closest('.monitor-row').remove();syncMonitorNames();if(monitorToggle.checked&&!monitorRows.children.length)addMonitorRow()}});syncMonitors();
</script>
@endsection
