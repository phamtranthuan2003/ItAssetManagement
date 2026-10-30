@extends('layouts.app')
@section('title','Bảng điều khiển')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Bảng điều khiển</h1><div class="text-secondary">Tổng quan vòng đời thiết bị và hoạt động kho.</div></div></div>
<div class="row g-3 mb-4">
    @foreach([['devices','Tổng thiết bị','primary'],['in_stock','Trong kho','success'],['allocated','Đang cấp phát','info'],['employees','Nhân viên đang làm việc','secondary'],['maintenance','Đang bảo trì','warning']] as [$key,$label,$color])
        <div class="col-sm-6 col-xl"><div class="card stat-card h-100"><div class="card-body"><div class="text-secondary small">{{ $label }}</div><div class="fs-3 fw-bold text-{{ $color }}">{{ number_format($counts[$key]) }}</div></div></div></div>
    @endforeach
</div>
<section class="card"><div class="card-header bg-white fw-semibold">Lịch sử thiết bị gần đây</div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Ngày</th><th>Mã thiết bị</th><th>Hoạt động</th><th>Mô tả</th></tr></thead><tbody>
@forelse($recentHistory as $row)<tr><td>{{ $row->action_date }}</td><td><a href="{{ route('devices.show',$row->device_id) }}">{{ $row->device_code }}</a></td><td>{{ $row->action_type }}</td><td>{{ $row->description }}</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-4">Chưa có hoạt động.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
