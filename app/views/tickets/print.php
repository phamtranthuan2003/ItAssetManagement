<?php
$ticketDate = new DateTimeImmutable($ticket['ticket_date']);

$fromName = $ticket['from_employee_name'] ?? '';
$fromPosition = $ticket['from_employee_pos'] ?? '';
$fromDepartment = $ticket['from_department_name'] ?? '';
$fromContact = implode(' · ', array_filter([
    $ticket['from_employee_phone'] ?? '',
    $ticket['from_employee_email'] ?? '',
], static fn ($value) => trim((string)$value) !== ''));

$toName = $ticket['to_employee_name'] ?? '';
$toPosition = $ticket['to_employee_pos'] ?? '';
$toDepartment = $ticket['to_department_name'] ?? '';
$toContact = implode(' · ', array_filter([
    $ticket['to_employee_phone'] ?? '',
    $ticket['to_employee_email'] ?? '',
], static fn ($value) => trim((string)$value) !== ''));

if ($ticket['ticket_type'] === 'allocation') {
    $fromName = $fromName ?: 'Phạm Trần Thuận';
    $fromPosition = $fromPosition ?: 'CV IT Helpdesk';
    $fromDepartment = $fromDepartment ?: 'Phòng Công nghệ Thông tin';
} elseif ($ticket['ticket_type'] === 'revocation') {
    $toName = $toName ?: 'Phòng Công nghệ Thông tin';
    $toPosition = $toPosition ?: 'Thủ Kho Thiết bị CNTT';
    $toDepartment = $toDepartment ?: 'Phòng Công nghệ Thông tin';
}

$handoverPurpose = match ($ticket['ticket_type']) {
    'allocation' => !empty($ticket['is_temporary'])
        ? 'Cấp phát thiết bị CNTT tạm thời cho người dùng'
        : 'Cấp phát thiết bị CNTT cho người dùng',
    'transfer' => 'Điều chuyển thiết bị CNTT cho người dùng',
    'revocation' => 'Thu hồi thiết bị CNTT',
    default => 'Bàn giao thiết bị CNTT',
};

$handoverOption = match (true) {
    $ticket['ticket_type'] === 'transfer' => 'transfer',
    $ticket['ticket_type'] === 'allocation' && !empty($ticket['is_temporary']) => 'temporary',
    $ticket['ticket_type'] === 'allocation' => 'new',
    default => null,
};

$conditionLabels = [
    'new' => 'Mới',
    'used' => 'Đang sử dụng',
    'broken' => 'Hỏng',
    'under_warranty' => 'Còn bảo hành',
    'good' => 'Tốt',
    'missing_accessories' => 'Thiếu phụ kiện',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biên bản bàn giao tài sản CNTT - <?= e($ticket['ticket_code']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 13mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111;
            background: #e9ecef;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
        }

        .toolbar {
            position: sticky;
            top: 0;
            padding: 10px;
            background: #fff;
            border-bottom: 1px solid #ced4da;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .toolbar button {
            padding: 8px 18px;
            border: 0;
            border-radius: 4px;
            color: #fff;
            background: #198754;
            cursor: pointer;
            font-size: 14px;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            padding: 14mm 13mm;
            background: #fff;
            box-shadow: 0 2px 12px rgb(0 0 0 / 15%);
        }

        .header {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 12px;
            text-align: center;
            font-size: 10pt;
        }

        .header strong,
        .header span {
            display: block;
        }

        .header .company {
            text-transform: uppercase;
            font-weight: bold;
        }

        .header .national-title {
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .motto {
            font-weight: bold;
        }

        .header .rule {
            width: 65%;
            margin: 4px auto;
            border-bottom: 1px solid #111;
        }

        h1 {
            margin: 22px 0 2px;
            text-align: center;
            font-size: 15pt;
            text-transform: uppercase;
        }

        .subtitle {
            margin: 0 0 14px;
            text-align: center;
            font-style: italic;
        }

        .meta {
            margin: 10px 0;
        }

        .meta-line {
            margin: 3px 0;
        }

        .type-options {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 10px 0 14px;
        }

        .type-option {
            white-space: nowrap;
        }

        .box {
            display: inline-flex;
            width: 13px;
            height: 13px;
            margin-right: 4px;
            align-items: center;
            justify-content: center;
            border: 1px solid #111;
            vertical-align: -2px;
            font-size: 11px;
            font-weight: bold;
        }

        .section-title {
            margin: 13px 0 6px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .party {
            margin: 0 0 8px;
        }

        .party-title {
            margin: 7px 0 4px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .party-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10pt;
        }

        .party-table th,
        .party-table td {
            border: 1px solid #111;
            padding: 15px 6px;
            vertical-align: top;
        }

        .party-table th {
            text-align: center;
        }

        .party-table .label {
            width: 19%;
            font-weight: bold;
        }

        .party-table .value {
            width: 31%;
        }

        .asset-table {
            width: 100%;
            margin: 8px 0 10px;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9pt;
        }

        .asset-table th,
        .asset-table td {
            border: 1px solid #111;
            padding: 5px 4px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .asset-table th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .asset-table .center {
            text-align: center;
        }

        .asset-table .spec-line {
            margin: 1px 0;
        }

        .asset-table tbody tr {
            page-break-inside: avoid;
        }

        .commitment {
            margin: 10px 0;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 24px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-title {
            min-height: 32px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-instruction {
            min-height: 72px;
            font-size: 9pt;
            font-style: italic;
        }

        .signature-name {
            font-weight: bold;
        }

        .muted {
            color: #555;
        }

        @media print {
            body {
                background: #fff;
                font-size: 10pt;
            }

            .toolbar {
                display: none;
            }

            .paper {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" onclick="window.print()">In / Lưu PDF</button>
</div>

<main class="paper">
    <header class="header">
        <div>
            <span class="company">CÔNG TY CỔ PHẦN TẬP ĐOÀN EVERLAND</span>
            <span>PHÒNG CÔNG NGHỆ THÔNG TIN</span>
            <div class="rule"></div>
            <span>Số: ……/BBBG-CNTT</span>
        </div>
        <div>
            <span class="national-title">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</span>
            <span class="motto">Độc lập - Tự do - Hạnh phúc</span>
            <div class="rule"></div>
            <span>Ngày <?= $ticketDate->format('d') ?> tháng <?= $ticketDate->format('m') ?> năm <?= $ticketDate->format('Y') ?></span>
        </div>
    </header>

    <h1>Biên bản bàn giao tài sản / thiết bị CNTT</h1>
    <p class="subtitle">(V/v: <?= e($handoverPurpose) ?>)</p>

    <div class="type-options" aria-label="Loại bàn giao">
        <?php foreach ([
            'new' => 'Cấp mới',
            'replacement' => 'Cấp thay thế',
            'transfer' => 'Điều chuyển',
            'temporary' => 'Cấp tạm thời',
        ] as $option => $label): ?>
            <span class="type-option">
                <span class="box"><?= $handoverOption === $option ? 'x' : '' ?></span>
                <?= e($label) ?>
            </span>
        <?php endforeach; ?>
    </div>

    <div class="section-title">I. Các bên tham gia</div>
    <section class="party">
        <div class="party-title">Bên giao (Bên A): <?= e($fromDepartment ?: 'Phòng Công nghệ Thông tin') ?></div>
        <table class="party-table">
            <thead>
                <tr>
                    <th>Họ và tên</th>
                    <th>Chức vụ</th>
                    <th>Điện thoại / Email</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="party">
        <div class="party-title">Bên nhận (Bên B): <?= e($ticket['ticket_type'] === 'revocation' ? 'Phòng Công nghệ Thông tin' : 'Người sử dụng') ?></div>
        <table class="party-table">
            <tbody>
                <tr>
                    <td class="label">Họ và tên</td>
                    <td class="value"><?= e($toName ?: '........................................................') ?></td>
                    <td class="label">Mã nhân viên</td>
                    <td class="value">........................................</td>
                </tr>
                <tr>
                    <td class="label">Chức vụ</td>
                    <td><?= e($toPosition ?: '........................................................') ?></td>
                    <td class="label">Phòng ban / Đơn vị</td>
                    <td><?= e($toDepartment ?: '........................................') ?></td>
                </tr>
                <tr>
                    <td class="label">Công ty thành viên</td>
                    <td>........................................................</td>
                    <td class="label">Vị trí làm việc</td>
                    <td>........................................</td>
                </tr>
                <tr>
                    <td class="label">Điện thoại / Email</td>
                    <td colspan="3"><?= e($toContact) ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section>
        <p>Hai bên cùng tiến hành bàn giao tài sản, thiết bị CNTT với nội dung như sau:</p>
        <div class="section-title">II. Danh mục tài sản / thiết bị bàn giao</div>
        <?php if (!empty($ticket['reason'])): ?>
            <p>Lý do bàn giao: <?= e($ticket['reason']) ?></p>
        <?php endif; ?>
        <table class="asset-table">
            <colgroup>
                <col style="width: 6%">
                <col style="width: 23%">
                <col style="width: 31%">
                <col style="width: 7%">
                <col style="width: 15%">
                <col style="width: 18%">
            </colgroup>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thiết bị</th>
                    <th>Cấu hình</th>
                    <th>SL</th>
                    <th>Tình trạng</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $index => $row): ?>
                    <?php
                    $configuration = [];
                    foreach ([
                        'CPU' => $row['cpu'] ?? null,
                        'Mainboard' => $row['mainboard'] ?? null,
                        'RAM' => !empty($row['ram_gb'])
                            ? (int)$row['ram_gb'] . ' GB' . (!empty($row['ram_type']) ? ' ' . $row['ram_type'] : '')
                            : null,
                        'SSD' => $row['ssd_capacity'] ?? null,
                        'HDD' => $row['hdd_capacity'] ?? null,
                        'GPU' => $row['gpu'] ?? null,
                        'Màn hình' => !empty($row['screen_size']) ? $row['screen_size'] . ' inch' : null,
                    ] as $label => $value) {
                        if ($value !== null && trim((string)$value) !== '') {
                            $configuration[] = $label . ': ' . $value;
                        }
                    }

                    $deviceName = trim(implode(' ', array_filter([
                        $row['type'] ?? '',
                        $row['brand'] ?? '',
                        $row['model'] ?? '',
                    ], static fn ($value) => trim((string)$value) !== '')));
                    $condition = $row['returned_condition'] ?: ($row['condition_state'] ?? '');
                    $conditionLabel = $conditionLabels[$condition] ?? '................................';

                    $notes = array_filter([
                        $row['serial_number'] ? 'Serial: ' . $row['serial_number'] : null,
                        $row['accessories'] ?? null,
                        !empty($row['paired_pc_code']) ? 'Ghép với PC ' . $row['paired_pc_code'] : null,
                        !empty($row['paired_monitor_list']) ? 'Màn hình kèm: ' . $row['paired_monitor_list'] : null,
                        $row['notes'] ?? null,
                    ], static fn ($value) => trim((string)$value) !== '');
                    ?>
                    <tr>
                        <td class="center"><?= $index + 1 ?></td>
                        <td>
                            <strong><?= e($deviceName ?: 'Thiết bị') ?></strong>
                            <div class="muted"><?= e($row['device_code'] ?? '') ?></div>
                        </td>
                        <td>
                            <?php if ($configuration): ?>
                                <?php foreach ($configuration as $line): ?>
                                    <div class="spec-line"><?= e($line) ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="muted">Không có thông số cấu hình</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">1</td>
                        <td><?= e($conditionLabel) ?></td>
                        <td><?= $notes ? e(implode('; ', $notes)) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($details)): ?>
                    <tr>
                        <td colspan="6" class="center">Không có thiết bị trong phiếu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <?php if (!empty($ticket['is_temporary'])): ?>
        <p class="commitment">
            <strong>Thời hạn hoàn trả:</strong>
            <?= !empty($ticket['expected_return_date']) ? formatDate($ticket['expected_return_date']) : 'Chưa xác định' ?>.
            Bên nhận có trách nhiệm hoàn trả tài sản đúng thời hạn.
        </p>
    <?php endif; ?>
    <?php if (!empty($ticket['notes'])): ?>
        <p><strong>Ghi chú:</strong> <?= e($ticket['notes']) ?></p>
    <?php endif; ?>
    <p class="commitment">
        Bên nhận đã kiểm tra và nhận đủ thiết bị như trên. Biên bản lập thành 02 bản, mỗi bên giữ 01 bản.
    </p>

    <section class="signatures">
        <div>
            <div class="signature-title">Quản lý tài sản</div>
            <div class="signature-instruction">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name"><?= e($fromName) ?></div>
        </div>
        <div>
            <div class="signature-title">ĐẠI DIỆN BÊN NHẬN</div>
            <div class="signature-instruction">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name"><?= e($toName) ?></div>
        </div>
        <div>
            <div class="signature-title">Phòng CNTT</div>
            <div class="signature-instruction">(Ký, ghi rõ họ tên)</div>
            <div class="signature-name">Vũ Minh Đức</div>
        </div>
    </section>
</main>
</body>
</html>
