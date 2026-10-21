<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Biên bản bàn giao - <?= e($ticket['ticket_code']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f8f9fa;
            font-size: 14pt;
            line-height: 1.4;
        }
        .page-print {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        .document-title {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16pt;
            margin: 20px 0 5px;
            text-align: center;
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12pt;
        }
        .table-print th, .table-print td {
            border: 1px solid #000;
            padding: 6px 10px;
        }
        .table-print th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .signature-box {
            text-align: center;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-role {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12pt;
        }
        .signature-note {
            font-style: italic;
            font-size: 11pt;
            margin-bottom: 70px;
        }
        .signature-name {
            font-weight: bold;
        }
        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .page-print {
                width: 100%;
                min-height: auto;
                padding: 10mm;
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Nút in (Ẩn khi in) -->
<div class="no-print text-center py-3 bg-white border-bottom shadow-sm sticky-top">
    <button onclick="window.print()" class="btn btn-primary px-4 me-2">
        <i class="bi bi-printer me-1"></i> In biên bản này (Ctrl + P)
    </button>
    <button onclick="window.close()" class="btn btn-secondary px-3">
        Đóng cửa sổ
    </button>
</div>

<div class="page-print">
    <!-- TIÊU NGỮ QUỐC GIA -->
    <div class="row">
        <div class="col-6 text-center">
            <strong>CÔNG TY CỔ PHẦN CÔNG NGHỆ</strong><br>
            <span>PHÒNG HÀNH CHÍNH & CNTT</span><br>
            <span class="small font-monospace">Số: <?= e($ticket['ticket_code']) ?></span>
        </div>
        <div class="col-6 text-center">
            <strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong><br>
            <strong>Độc lập - Tự do - Hạnh phúc</strong><br>
            <span style="letter-spacing: 2px;">---------o0o---------</span>
        </div>
    </div>

    <!-- TIÊU ĐỀ BIÊN BẢN -->
    <div class="document-title">
        <?= match ($ticket['ticket_type']) {
            'allocation' => 'BIÊN BẢN BÀN GIAO THIẾT BỊ CNTT',
            'transfer'   => 'BIÊN BẢN ĐIỀU CHUYỂN THIẾT BỊ CNTT',
            'revocation' => 'BIÊN BẢN THU HỒI THIẾT BỊ CNTT',
            default      => 'BIÊN BẢN BÀN GIAO THIẾT BỊ'
        } ?>
    </div>
    <div class="text-center font-italic small mb-4">
        Hôm nay, ngày <?= date('d', strtotime($ticket['ticket_date'])) ?> tháng <?= date('m', strtotime($ticket['ticket_date'])) ?> năm <?= date('Y', strtotime($ticket['ticket_date'])) ?>, tại Văn phòng Công ty, chúng tôi gồm có:
    </div>

    <!-- THÔNG TIN CÁC BÊN -->
    <p class="mb-1"><strong>I. ĐẠI DIỆN BÊN GIAO (BÊN A):</strong></p>
    <ul class="list-unstyled ps-3 mb-3">
        <li>- Họ và tên: <strong><?= e($ticket['from_employee_name'] ?: 'Bộ phận Quản lý Kho CNTT') ?></strong></li>
        <li>- Chức vụ: <?= e($ticket['from_employee_pos'] ?: 'Thủ kho / Kỹ thuật viên') ?></li>
        <li>- Đơn vị / Phòng ban: <?= e($ticket['from_department_name'] ?: 'Phòng CNTT') ?></li>
    </ul>

    <p class="mb-1"><strong>II. ĐẠI DIỆN BÊN NHẬN (BÊN B):</strong></p>
    <ul class="list-unstyled ps-3 mb-3">
        <li>- Họ và tên: <strong><?= e($ticket['to_employee_name'] ?: 'Bộ phận Quản lý Kho CNTT') ?></strong></li>
        <li>- Chức vụ: <?= e($ticket['to_employee_pos'] ?: 'Cán bộ tiếp nhận') ?></li>
        <li>- Đơn vị / Phòng ban: <?= e($ticket['to_department_name'] ?: 'Kho thiết bị') ?></li>
    </ul>

    <p class="mb-2"><strong>III. NỘI DUNG BÀN GIAO:</strong></p>
    <p class="ps-3 mb-2">
        Bên A tiến hành bàn giao cho Bên B quản lý và sử dụng các trang thiết bị máy móc làm việc với thông số kỹ thuật chi tiết như sau:
        <?php if (!empty($ticket['reason'])): ?>
            <br><em>(Lý do: <?= e($ticket['reason']) ?>)</em>
        <?php endif; ?>
    </p>

    <!-- BẢNG THIẾT BỊ BÀN GIAO -->
    <table class="table-print">
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 18%;">STT thiết bị</th>
                <th style="width: 25%;">Chủng loại / Model</th>
                <th style="width: 18%;">Số Serial</th>
                <th style="width: 34%;">Cấu hình & Phụ kiện đi kèm</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            foreach ($details as $row): 
            ?>
            <tr>
                <td class="text-center"><?= $i++ ?></td>
                <td><strong><?= (int)$row['device_id'] ?></strong></td>
                <td><?= e($row['brand']) ?> <?= e($row['model']) ?> (<?= e($row['type']) ?>)</td>
                <td class="font-monospace text-center"><?= e($row['serial_number'] ?? '—') ?></td>
                <td>
                    <div><strong>CPU:</strong> <?= e($row['cpu']) ?>, <strong>RAM:</strong> <?= e($row['ram_gb']) ?>GB</div>
                    <div><strong>Ổ cứng:</strong> <?= e($row['ssd_capacity'] ?? '—') ?></div>
                    <div><strong>Phụ kiện:</strong> <?= !empty($row['accessories']) ? e($row['accessories']) : 'Thân máy' ?></div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="ps-3 mb-1">
        <?php if ($ticket['is_temporary']): ?>
            <strong>* Ghi chú thời hạn:</strong> Đây là biên bản cấp phát tạm thời. Bên nhận có trách nhiệm hoàn trả thiết bị về kho trước ngày <strong><?= formatDate($ticket['expected_return_date']) ?></strong>.
        <?php else: ?>
            <strong>* Cam kết trách nhiệm:</strong> Bên B có trách nhiệm sử dụng, bảo quản tài sản được cấp phát theo đúng quy định của Công ty. Nếu xảy ra hư hỏng do lỗi chủ quan hoặc mất mát phụ kiện, Bên B phải hoàn toàn chịu trách nhiệm bồi hoàn theo quy chế.
        <?php endif; ?>
    </p>

    <div class="ps-3 mb-3">
        Biên bản được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản để làm căn cứ quản lý tài sản.
    </div>

    <!-- KHỐI CHỮ KÝ 4 BÊN -->
    <div class="row signature-box">
        <div class="col-3">
            <div class="signature-role">BÊN GIAO</div>
            <div class="signature-note">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name"><?= e($ticket['from_employee_name'] ?: 'Đại diện kho') ?></div>
        </div>
        <div class="col-3">
            <div class="signature-role">BÊN NHẬN</div>
            <div class="signature-note">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name"><?= e($ticket['to_employee_name'] ?: 'Đại diện kho') ?></div>
        </div>
        <div class="col-3">
            <div class="signature-role">NGƯỜI LẬP PHIẾU</div>
            <div class="signature-note">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name"><?= e($ticket['creator_name']) ?></div>
        </div>
        <div class="col-3">
            <div class="signature-role">PHÊ DUYỆT</div>
            <div class="signature-note">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name">Ban Giám Đốc / TP. IT</div>
        </div>
    </div>
</div>

</body>
</html>
