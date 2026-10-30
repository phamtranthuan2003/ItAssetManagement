@extends('layouts.app')
@section('title','Phiếu thiết bị')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h1 class="h3 mb-1">Cấp phát, chuyển giao, thu hồi</h1><p class="text-secondary mb-0">Mọi thay đổi thiết bị được ghi vào lịch sử và phiếu.</p></div>@if(auth()->user()->hasPermission('tickets.create'))<a class="btn btn-primary" href="{{ route('tickets.create') }}">Lập phiếu</a>@endif</div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Mã phiếu</th><th>Ngày</th><th>Loại phiếu</th><th>Người nhận</th><th>Trạng thái</th><th></th></tr></thead><tbody>
@forelse($tickets as $ticket)<tr><td><a href="{{ route('tickets.show',$ticket->id) }}">{{ $ticket->ticket_code }}</a></td><td>{{ $ticket->ticket_date }}</td><td>{{ ['allocation'=>'Cấp phát','transfer'=>'Chuyển giao','revocation'=>'Thu hồi'][$ticket->ticket_type] ?? $ticket->ticket_type }}</td><td>{{ $ticket->receiver_name ?: '—' }}</td><td>{{ $ticket->status }}</td><td class="text-end">@if(auth()->user()->hasPermission('tickets.print'))<a class="btn btn-sm btn-outline-secondary" href="{{ route('tickets.print',$ticket->id) }}">In</a>@endif</td></tr>@empty<tr><td colspan="6" class="text-center py-4">Chưa có phiếu.</td></tr>@endforelse
</tbody></table></div><div class="card-body">{{ $tickets->links() }}</div></div>
@endsection
