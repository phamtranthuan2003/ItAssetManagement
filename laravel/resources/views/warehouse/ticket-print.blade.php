<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biên bản bàn giao - {{ $ticket->ticket_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{font-family:"Times New Roman",serif;color:#000;background:#f8f9fa;font-size:14pt;line-height:1.4}
        .page-print{width:210mm;min-height:297mm;padding:20mm;margin:20px auto;background:#fff;box-shadow:0 0 10px #0002}
        .document-title{text-transform:uppercase;font-weight:bold;font-size:16pt;margin:20px 0 5px;text-align:center}
        .table-print{width:100%;border-collapse:collapse;margin:15px 0;font-size:11pt}
        .table-print th,.table-print td{border:1px solid #000;padding:6px 8px;vertical-align:top}
        .table-print th{background:#f2f2f2;text-align:center}
        .signature-box{text-align:center;margin-top:25px;page-break-inside:avoid}
        .signature-role{font-weight:bold;text-transform:uppercase;font-size:11pt}
        .signature-note{font-style:italic;font-size:10pt;margin-bottom:65px}
        .signature-name{font-weight:bold}
        @media print{body{background:#fff;margin:0}.page-print{width:100%;min-height:auto;padding:10mm;margin:0;box-shadow:none}.no-print{display:none!important}}
    </style>
</head>
<body>
<div class="no-print text-center py-3 bg-white border-bottom"><button onclick="window.print()" class="btn btn-primary px-4 me-2">In biên bản này (Ctrl + P)</button><a href="{{ route('tickets.show',$ticket->id) }}" class="btn btn-secondary">Đóng</a></div>
<main class="page-print">
    <div class="row">
        <div class="col-6 text-center"><strong>CÔNG TY CỔ PHẦN CÔNG NGHỆ</strong><br><span>PHÒNG HÀNH CHÍNH & CNTT</span><br><span>Số: {{ $ticket->ticket_code }}</span></div>
        <div class="col-6 text-center"><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong><br><strong>Độc lập - Tự do - Hạnh phúc</strong><br>---------o0o---------</div>
    </div>
    <h1 class="document-title">{{ ['allocation'=>'Biên bản bàn giao thiết bị CNTT','transfer'=>'Biên bản điều chuyển thiết bị CNTT','revocation'=>'Biên bản thu hồi thiết bị CNTT'][$ticket->ticket_type] ?? 'Biên bản bàn giao thiết bị' }}</h1>
    <p class="text-center fst-italic small mb-4">Hôm nay, ngày {{ \Illuminate\Support\Carbon::parse($ticket->ticket_date)->format('d') }} tháng {{ \Illuminate\Support\Carbon::parse($ticket->ticket_date)->format('m') }} năm {{ \Illuminate\Support\Carbon::parse($ticket->ticket_date)->format('Y') }}, tại Văn phòng Công ty, chúng tôi gồm có:</p>
    <p class="mb-1"><strong>I. ĐẠI DIỆN BÊN GIAO (BÊN A):</strong></p><ul class="list-unstyled ps-3 mb-3">
        <li>- Họ và tên: <strong>{{ $ticket->from_employee_name ?: 'Bộ phận Quản lý Kho CNTT' }}</strong></li>
        <li>- Chức vụ: {{ $ticket->from_employee_pos ?: 'Thủ kho / Kỹ thuật viên' }}</li>
        <li>- Đơn vị / Phòng ban: {{ $ticket->from_department_name ?: 'Phòng CNTT' }}</li>
    </ul>
    <p class="mb-1"><strong>II. ĐẠI DIỆN BÊN NHẬN (BÊN B):</strong></p><ul class="list-unstyled ps-3 mb-3">
        <li>- Họ và tên: <strong>{{ $ticket->to_employee_name ?: 'Bộ phận Quản lý Kho CNTT' }}</strong></li>
        <li>- Chức vụ: {{ $ticket->to_employee_pos ?: 'Cán bộ tiếp nhận' }}</li>
        <li>- Đơn vị / Phòng ban: {{ $ticket->to_department_name ?: 'Kho thiết bị' }}</li>
    </ul>
    <p class="mb-2"><strong>III. NỘI DUNG BÀN GIAO:</strong></p>
    <p class="ps-3 mb-2">Bên A tiến hành bàn giao cho Bên B quản lý và sử dụng các trang thiết bị máy móc làm việc với thông số kỹ thuật chi tiết như sau:
        @if($ticket->reason)<br><em>(Lý do: {{ $ticket->reason }})</em>@endif
    </p>
    <table class="table-print"><thead><tr><th style="width:5%">STT</th><th style="width:16%">Mã thiết bị</th><th style="width:24%">Chủng loại / Model</th><th style="width:16%">Số Serial</th><th style="width:39%">Cấu hình & phụ kiện đi kèm</th></tr></thead><tbody>
    @forelse($items as $index=>$item)<tr><td class="text-center">{{ $index+1 }}</td><td><strong>{{ $item->device_code }}</strong></td><td>{{ $item->brand }} {{ $item->model }} ({{ $item->type }})</td><td class="text-center">{{ $item->serial_number ?: '—' }}</td><td><div><strong>CPU:</strong> {{ $item->cpu ?: '—' }}, <strong>RAM:</strong> {{ $item->ram_gb ? $item->ram_gb.'GB' : '—' }}</div><div><strong>Ổ cứng:</strong> {{ $item->ssd_capacity ?: '—' }}</div><div><strong>Phụ kiện:</strong> {{ $item->accessories ?: 'Thân máy' }}</div>@if($item->returned_condition)<div><strong>Tình trạng thu hồi:</strong> {{ ['good'=>'Tốt','broken'=>'Hỏng','missing_accessories'=>'Thiếu phụ kiện'][$item->returned_condition] ?? $item->returned_condition }}</div>@endif</td></tr>
    @empty<tr><td colspan="5" class="text-center">Phiếu chưa có thiết bị.</td></tr>@endforelse
    </tbody></table>
    <p class="ps-3 mb-1">@if($ticket->is_temporary)<strong>* Ghi chú thời hạn:</strong> Đây là biên bản cấp phát tạm thời. Bên nhận có trách nhiệm hoàn trả thiết bị về kho trước ngày <strong>{{ $ticket->expected_return_date ?: '—' }}</strong>.@else<strong>* Cam kết trách nhiệm:</strong> Bên B có trách nhiệm sử dụng, bảo quản tài sản được cấp phát theo đúng quy định của Công ty. Nếu xảy ra hư hỏng do lỗi chủ quan hoặc mất mát phụ kiện, Bên B phải chịu trách nhiệm theo quy chế.@endif</p>
    <p class="ps-3 mb-3">Biên bản được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản để làm căn cứ quản lý tài sản.</p>
    <div class="row signature-box"><div class="col-3"><div class="signature-role">BÊN GIAO</div><div class="signature-note">(Ký, ghi rõ họ tên)</div><div class="signature-name">{{ $ticket->from_employee_name ?: 'Đại diện kho' }}</div></div><div class="col-3"><div class="signature-role">BÊN NHẬN</div><div class="signature-note">(Ký, ghi rõ họ tên)</div><div class="signature-name">{{ $ticket->to_employee_name ?: 'Đại diện kho' }}</div></div><div class="col-3"><div class="signature-role">NGƯỜI LẬP PHIẾU</div><div class="signature-note">(Ký, ghi rõ họ tên)</div><div class="signature-name">{{ $ticket->creator_name }}</div></div><div class="col-3"><div class="signature-role">PHÊ DUYỆT</div><div class="signature-note">(Ký, ghi rõ họ tên)</div><div class="signature-name">Ban Giám Đốc / TP. IT</div></div></div>
</main>
</body>
</html>
