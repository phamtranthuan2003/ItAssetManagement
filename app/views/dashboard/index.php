<!-- CẢNH BÁO NẾU CÓ THIẾT BỊ QUÁ HẠN TRẢ HOẶC SẮP HẾT HẠN BẢO HÀNH -->
<?php if (!empty($overdueDevices)): ?>
<div class="alert alert-danger shadow-sm border-danger d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-clock-history fs-3 text-danger me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Cảnh báo: Có <?= count($overdueDevices) ?> thiết bị mượn tạm ĐÃ QUÁ HẠN TRẢ!</h6>
            <p class="mb-0 small">Vui lòng kiểm tra và liên hệ nhân sự để thu hồi lại kho theo đúng quy định.</p>
        </div>
    </div>
    <a href="<?= url('devices?is_overdue=1') ?>" class="btn btn-danger btn-sm text-nowrap">
        <i class="bi bi-eye me-1"></i> Xem danh sách quá hạn
    </a>
</div>
<?php endif; ?>

<?php if (!empty($expiringDevices)): ?>
<div class="alert alert-warning shadow-sm border-warning d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-shield-exclamation fs-3 text-warning me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Thông báo: Có <?= count($expiringDevices) ?> thiết bị sắp hết hạn bảo hành (trong 30 ngày tới)!</h6>
            <p class="mb-0 small">Kế hoạch kiểm tra phần cứng hoặc gia hạn gói bảo hành với nhà phân phối.</p>
        </div>
    </div>
    <a href="<?= url('devices?expiring_warranty=1') ?>" class="btn btn-warning btn-sm text-dark text-nowrap">
        <i class="bi bi-eye me-1"></i> Xem danh sách
    </a>
</div>
<?php endif; ?>

<!-- 4 THẺ THỐNG KÊ CHÍNH -->
<div class="row g-3 mb-4">
    <!-- Tổng thiết bị -->
    <div class="col-xl-3 col-sm-6">
        <a href="<?= url('devices') ?>" class="stat-card-link">
            <div class="card stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Tổng thiết bị</span>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total']) ?></h3>
                        <div class="small text-muted mt-1">
                            <i class="bi bi-pc me-1"></i>PC: <strong><?= $stats['pc_count'] ?></strong> |
                            <i class="bi bi-laptop me-1"></i>Laptop: <strong><?= $stats['laptop_count'] ?></strong> |
                            <i class="bi bi-box me-1"></i>Khác: <strong><?= $stats['other_count'] ?></strong>
                        </div>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Trong kho -->
    <div class="col-xl-3 col-sm-6">
        <a href="<?= url('devices?status=in_stock') ?>" class="stat-card-link">
            <div class="card stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Trong kho (Sẵn sàng)</span>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($stats['in_stock']) ?></h3>
                        <div class="small text-muted mt-1">Có thể cấp phát ngay</div>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Đang cấp phát -->
    <div class="col-xl-3 col-sm-6">
        <a href="<?= url('devices?status=allocated') ?>" class="stat-card-link">
            <div class="card stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Đang cấp phát</span>
                        <h3 class="fw-bold mb-0 text-primary"><?= number_format($stats['allocated']) ?></h3>
                        <div class="small text-muted mt-1">Đang được nhân viên dùng</div>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Đang bảo trì -->
    <div class="col-xl-3 col-sm-6">
        <a href="<?= url('devices?status=maintenance') ?>" class="stat-card-link">
            <div class="card stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Đang sửa chữa / Bảo trì</span>
                        <h3 class="fw-bold mb-0 text-warning"><?= number_format($stats['maintenance']) ?></h3>
                        <div class="small text-muted mt-1">Thanh lý: <?= $stats['liquidated'] ?> thiết bị</div>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- NÚT THAO TÁC NHANH -->
<div class="card mb-4 bg-primary text-white p-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-lightning-charge-fill me-2"></i>Thao tác nhanh</h5>
            <p class="mb-0 text-white-50 small">Khởi tạo nhanh các giao dịch xuất nhập kho và cấp phát thiết bị</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php if (Auth::hasPermission('devices.create')): ?>
            <a href="<?= url('devices/create') ?>" class="btn btn-light fw-semibold">
                <i class="bi bi-plus-circle text-primary me-1"></i> Nhập thiết bị mới
            </a>
            <?php endif; ?>
            <?php if (Auth::hasPermission('tickets.create')): ?>
            <a href="<?= url('tickets/create?type=allocation') ?>" class="btn btn-success fw-semibold">
                <i class="bi bi-box-arrow-right me-1"></i> Lập phiếu cấp phát
            </a>
            <a href="<?= url('tickets/create?type=transfer') ?>" class="btn btn-info text-white fw-semibold">
                <i class="bi bi-arrow-left-right me-1"></i> Chuyển giao thiết bị
            </a>
            <a href="<?= url('tickets/create?type=revocation') ?>" class="btn btn-danger fw-semibold">
                <i class="bi bi-box-arrow-in-left me-1"></i> Lập phiếu thu hồi
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- PHÂN HỆ 1: THEO DÕI CẤP PHÁT THIẾT BỊ THEO THÁNG (3 THÁNG) -->
<!-- ======================================================== -->
<?php 
$monthsData = $monthlyAllocation['months'] ?? [];
$peakKey = $monthlyAllocation['peak_key'] ?? null;
$peakMonthInfo = $monthsData[$peakKey] ?? null;
?>
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fs-5 text-primary"><i class="bi bi-calendar3-range-fill"></i></span>
                <h5 class="fw-bold mb-0 text-dark">Thống Kê Cấp Phát Thiết Bị Theo Tháng</h5>
                <?php if ($peakMonthInfo && $peakMonthInfo['device_count'] > 0): ?>
                <span class="badge bg-warning text-dark border border-warning-subtle d-inline-flex align-items-center gap-1">
                    <i class="bi bi-trophy-fill text-dark"></i>
                    Tháng cao điểm: <strong><?= e($peakMonthInfo['display']) ?></strong> (<?= (int)$peakMonthInfo['device_count'] ?> thiết bị)
                </span>
                <?php endif; ?>
            </div>
            <div class="text-muted small mt-1">So sánh tình hình cấp phát giữa <strong>Tháng này</strong>, <strong>Tháng trước</strong> và <strong>Tháng trước nữa</strong> để xác định tháng cấp phát nhiều nhất</div>
        </div>
        <div>
            <a href="<?= url('tickets?type=allocation') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-list-check me-1"></i> Xem toàn bộ phiếu cấp phát
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- 3 Thẻ tháng -->
        <div class="row g-3 mb-4">
            <?php foreach ($monthsData as $mKey => $m): 
                $isPeak = ($peakKey === $mKey && $m['device_count'] > 0);
            ?>
            <div class="col-md-4">
                <div class="card h-100 <?= $isPeak ? 'peak-month-card' : 'border' ?> p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold"><?= e($m['label']) ?></span>
                            <h6 class="fw-bold text-dark mb-0"><?= e($m['month_name']) ?></h6>
                        </div>
                        <?php if ($isPeak): ?>
                        <span class="peak-badge shadow-sm">
                            <i class="bi bi-trophy-fill me-1"></i>Cấp phát nhiều nhất
                        </span>
                        <?php else: ?>
                        <span class="badge bg-light text-secondary border"><?= e($m['short_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="fw-bold mb-0 <?= $isPeak ? 'text-primary' : 'text-dark' ?>">
                            <?= number_format($m['device_count']) ?>
                        </h2>
                        <span class="text-muted small">thiết bị</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top small text-muted">
                        <span><i class="bi bi-file-earmark-text me-1"></i>Tổng số phiếu: <strong><?= number_format($m['total'] ?? $m['ticket_count']) ?></strong></span>
                        <a href="#tab-<?= e($mKey) ?>" class="text-decoration-none fw-semibold month-tab-trigger" data-target-tab="tab-<?= e($mKey) ?>">
                            Xem danh sách <i class="bi bi-chevron-right small"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Biểu đồ cột so sánh & Danh sách chi tiết phiếu cấp phát -->
        <div class="row g-4 align-items-stretch">
            <!-- Biểu đồ cột -->
            <div class="col-lg-5">
                <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold small text-uppercase text-secondary">
                            <i class="bi bi-bar-chart-fill me-1 text-primary"></i>So sánh cấp phát qua 3 tháng
                        </span>
                        <span class="badge bg-white text-secondary border small">Đơn vị: Thiết bị</span>
                    </div>
                    <div class="flex-grow-1 position-relative" style="min-height: 280px;">
                        <canvas id="monthlyAllocationChart"></canvas>
                    </div>
                    <div class="small text-muted text-center mt-2">
                        <i class="bi bi-info-circle me-1"></i>Cột màu xanh đậm biểu diễn tháng có lượng thiết bị cấp phát cao nhất.
                    </div>
                </div>
            </div>

            <!-- Bảng danh sách phiếu cấp phát trong tháng (Cuộn lướt đến đâu load đến đó) -->
            <div class="col-lg-7">
                <div class="p-3 border rounded-3 bg-white h-100 d-flex flex-column">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold small text-uppercase text-secondary">
                                <i class="bi bi-receipt me-1 text-primary"></i>Phiếu cấp phát trong tháng
                            </span>
                            <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.72rem;">
                                <i class="bi bi-mouse2 me-1"></i>Cuộn để tải thêm tự động
                            </span>
                        </div>
                        <ul class="nav nav-pills nav-pills-sm" id="monthTicketsTab" role="tablist">
                            <?php 
                            $firstTab = true;
                            foreach ($monthsData as $mKey => $m): 
                                $isActive = ($firstTab && $peakKey === null) || ($peakKey === $mKey);
                                if ($firstTab && $peakKey === null) $firstTab = false;
                                $ticketTotal = (int)($m['total'] ?? $m['ticket_count']);
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-3 small <?= $isActive ? 'active' : '' ?>" 
                                        id="btn-tab-<?= e($mKey) ?>" 
                                        data-bs-toggle="pill" 
                                        data-bs-target="#tab-<?= e($mKey) ?>" 
                                        type="button" 
                                        role="tab">
                                    <?= e($m['label']) ?> (<?= $ticketTotal ?>)
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="tab-content flex-grow-1" id="monthTicketsTabContent">
                        <?php 
                        $firstPane = true;
                        foreach ($monthsData as $mKey => $m): 
                            $isActivePane = ($firstPane && $peakKey === null) || ($peakKey === $mKey);
                            if ($firstPane && $peakKey === null) $firstPane = false;
                            $ticketTotal = (int)($m['total'] ?? $m['ticket_count']);
                            $hasMore = !empty($m['has_more']);
                        ?>
                        <div class="tab-pane fade <?= $isActivePane ? 'show active' : '' ?>" id="tab-<?= e($mKey) ?>" role="tabpanel">
                            <?php if (empty($m['tickets'])): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Không có phiếu cấp phát nào trong <strong><?= e($m['month_name']) ?></strong>.
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-between align-items-center px-1 mb-2">
                                    <span class="small text-muted">
                                        Đang hiển thị: <strong id="count-loaded-<?= e($mKey) ?>" class="text-primary"><?= count($m['tickets']) ?></strong> / <strong><?= $ticketTotal ?></strong> phiếu
                                    </span>
                                    <span class="badge bg-light text-secondary border small">
                                        <?= e($m['month_name']) ?>
                                    </span>
                                </div>

                                <!-- Container cuộn vô tận cho từng tháng -->
                                <div class="monthly-tickets-container border rounded" 
                                     id="container-<?= e($mKey) ?>"
                                     data-month="<?= e($mKey) ?>" 
                                     data-page="1" 
                                     data-has-more="<?= $hasMore ? 'true' : 'false' ?>"
                                     data-total="<?= $ticketTotal ?>"
                                     style="max-height: 330px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light sticky-top shadow-sm" style="z-index: 5;">
                                            <tr>
                                                <th>Mã phiếu</th>
                                                <th>Ngày cấp</th>
                                                <th>Người nhận / Phòng ban</th>
                                                <th class="text-center">Số TB</th>
                                                <th class="text-end">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-tickets-<?= e($mKey) ?>">
                                            <?php foreach ($m['tickets'] as $t): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= url('tickets/' . (int)$t['id']) ?>" class="fw-semibold text-decoration-none">
                                                        <?= e($t['ticket_code']) ?>
                                                    </a>
                                                    <?php if (!empty($t['is_temporary'])): ?>
                                                        <span class="badge bg-warning-subtle text-warning border ms-1" title="Cấp tạm">Tạm</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small text-muted"><?= formatDate($t['ticket_date']) ?></td>
                                                <td>
                                                    <div class="fw-semibold small text-dark"><?= e($t['to_employee_name'] ?: 'Chưa gán') ?></div>
                                                    <div class="text-muted" style="font-size: 0.75rem;"><?= e($t['to_department_name'] ?: '---') ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold"><?= (int)$t['device_count'] ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= url('tickets/' . (int)$t['id']) ?>" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- Loading spinner khi cuộn tải thêm -->
                                    <div class="scroll-loading py-2 text-center d-none" id="loading-<?= e($mKey) ?>">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        <span class="small text-muted">Đang tải thêm phiếu cấp phát...</span>
                                    </div>

                                    <!-- Thông báo khi đã hiển thị hết toàn bộ -->
                                    <div class="scroll-end py-2 text-center small text-muted <?= $hasMore ? 'd-none' : '' ?>" id="end-<?= e($mKey) ?>">
                                        <i class="bi bi-check2-circle text-success me-1"></i>Đã hiển thị toàn bộ <strong><?= $ticketTotal ?></strong> phiếu cấp phát trong tháng.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- PHÂN HỆ 2: THIẾT BỊ PHÒNG BAN & BIỂU ĐỒ TRÒN Ở BÊN PHẢI -->
<!-- ======================================================== -->
<div class="row g-4 mb-4">
    <!-- CỘT TRÁI: DANH SÁCH THỐNG KÊ PHÒNG BAN -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                <span class="fw-bold text-dark"><i class="bi bi-buildings me-2 text-primary"></i>Danh Sách Thiết Bị Theo Phòng Ban</span>
                <a href="<?= url('departments') ?>" class="btn btn-sm btn-outline-primary py-0">Quản lý</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Phòng ban</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end" style="width: 140px;">Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalAllocated = max(1, $stats['allocated']);
                            foreach ($deptStats as $dept): 
                                $percent = round(($dept['device_count'] / $totalAllocated) * 100);
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= e(url('devices?department_id=' . (int)$dept['department_id'])) ?>" class="d-block text-decoration-none text-reset" title="Xem toàn bộ thiết bị của phòng ban này">
                                        <div class="fw-semibold text-dark"><?= e($dept['department_name']) ?></div>
                                        <small class="text-muted"><?= e($dept['department_code']) ?></small>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fs-6 fw-bold"><?= $dept['device_count'] ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <small class="text-muted fw-semibold"><?= $percent ?>%</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: BIỂU ĐỒ HÌNH TRÒN PHÂN BỔ PHÒNG BAN -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Biểu Đồ Phân Bổ Thiết Bị Phòng Ban</span>
                <span class="badge bg-light text-secondary border"><?= count($deptStats) ?> phòng ban</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <?php 
                $hasDeptData = false;
                foreach ($deptStats as $d) {
                    if ($d['device_count'] > 0) { $hasDeptData = true; break; }
                }
                ?>
                <?php if ($hasDeptData): ?>
                    <div style="position: relative; width: 100%; height: 320px;">
                        <canvas id="deptPieChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-pie-chart fs-1 text-secondary d-block mb-2"></i>
                        <p class="mb-0">Chưa có thiết bị nào được cấp phát cho các phòng ban.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- PHÂN HỆ 3: HOẠT ĐỘNG KHO GẦN ĐÂY (CHO NHỎ XUỐNG Ở BÊN DƯỚI) -->
<!-- ======================================================== -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-primary"><i class="bi bi-activity"></i></span>
                    <h6 class="fw-bold mb-0 text-dark">Hoạt Động Kho Gần Đây</h6>
                </div>
                <span class="badge bg-light text-dark border py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-dot text-success me-1"></i>Nhật ký thao tác thời gian thực
                </span>
            </div>
            <div class="card-body py-2 px-3">
                <div style="max-height: 220px; overflow-y: auto; padding-right: 6px;">
                    <?php if (empty($recentActivities)): ?>
                        <p class="text-muted text-center py-3 small mb-0">Chưa có hoạt động nào được ghi nhận.</p>
                    <?php else: ?>
                        <div class="timeline-compact">
                            <?php foreach ($recentActivities as $act): ?>
                            <div class="timeline-item">
                                <span class="timeline-dot"></span>
                                <div class="d-flex justify-content-between align-items-baseline gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold text-dark small"><?= e($act['performer_name']) ?></span>
                                        <span class="badge bg-light text-secondary border py-0 px-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-tag me-1"></i>STT <?= (int)$act['device_id'] ?> (<?= e($act['model'] ?: $act['type']) ?>)
                                        </span>
                                    </div>
                                    <small class="text-muted text-nowrap" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i><?= formatDateTime($act['action_date']) ?>
                                    </small>
                                </div>
                                <div class="text-secondary small mt-1" style="font-size: 0.82rem; line-height: 1.35;">
                                    <?= e($act['description']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- SCRIPT KHỞI TẠO BIỂU ĐỒ & CUỘN VÔ TẬN (INFINITE SCROLL) -->
<!-- ======================================================== -->
<?php
// Chuẩn bị dữ liệu JSON cho biểu đồ phòng ban
$filteredDeptStats = array_filter($deptStats, fn($d) => (int)$d['device_count'] > 0);
$deptLabels = array_values(array_column($filteredDeptStats, 'department_name'));
$deptCounts = array_values(array_map('intval', array_column($filteredDeptStats, 'device_count')));

// Chuẩn bị dữ liệu JSON cho biểu đồ cấp phát 3 tháng
$monthLabels = [];
$monthDeviceCounts = [];
$monthTicketCounts = [];
$monthBackgroundColors = [];

foreach ($monthsData as $mKey => $m) {
    $monthLabels[] = $m['display'];
    $monthDeviceCounts[] = (int)$m['device_count'];
    $monthTicketCounts[] = (int)($m['total'] ?? $m['ticket_count']);
    
    // Highlight cột tháng cao điểm
    if ($peakKey === $mKey && $m['device_count'] > 0) {
        $monthBackgroundColors[] = '#2563eb'; // Xanh đậm cho tháng cao điểm
    } else {
        $monthBackgroundColors[] = '#93c5fd'; // Xanh nhạt cho tháng khác
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyTicketsApiUrl = '<?= url("dashboard/monthly-tickets") ?>';

    // Helper escape chống XSS khi render JS
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(str).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // ==========================================
    // 1. CUỘN VÔ TẬN: LƯỚT ĐẾN ĐÂU LOAD ĐẾN ĐÓ
    // ==========================================
    const scrollContainers = document.querySelectorAll('.monthly-tickets-container');
    scrollContainers.forEach(function (container) {
        let isFetching = false;

        container.addEventListener('scroll', function () {
            const month = container.getAttribute('data-month');
            const hasMore = container.getAttribute('data-has-more') === 'true';
            let currentPage = parseInt(container.getAttribute('data-page') || '1', 10);
            const loadingEl = document.getElementById('loading-' + month);
            const endEl = document.getElementById('end-' + month);
            const tbody = document.getElementById('tbody-tickets-' + month);
            const counterEl = document.getElementById('count-loaded-' + month);

            if (!hasMore || isFetching) return;

            // Kiểm tra khi thanh cuộn gần chạm đáy container (còn cách 60px)
            const scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
            if (scrollBottom <= 60) {
                isFetching = true;
                if (loadingEl) loadingEl.classList.remove('d-none');

                const nextPage = currentPage + 1;
                const fetchUrl = monthlyTicketsApiUrl + '?month=' + encodeURIComponent(month) + '&page=' + nextPage + '&limit=15';

                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('Mã lỗi mạng: ' + response.status);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && Array.isArray(data.tickets) && data.tickets.length > 0) {
                            const frag = document.createDocumentFragment();

                            data.tickets.forEach(ticket => {
                                const tr = document.createElement('tr');
                                const tempBadge = ticket.is_temporary
                                    ? '<span class="badge bg-warning-subtle text-warning border ms-1" title="Cấp tạm">Tạm</span>'
                                    : '';

                                tr.innerHTML = `
                                    <td>
                                        <a href="${ticket.url}" class="fw-semibold text-decoration-none">
                                            ${escapeHtml(ticket.ticket_code)}
                                        </a>
                                        ${tempBadge}
                                    </td>
                                    <td class="small text-muted">${escapeHtml(ticket.ticket_date)}</td>
                                    <td>
                                        <div class="fw-semibold small text-dark">${escapeHtml(ticket.to_employee_name)}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">${escapeHtml(ticket.to_department_name)}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">${parseInt(ticket.device_count, 10)}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="${ticket.url}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                `;
                                frag.appendChild(tr);
                            });

                            tbody.appendChild(frag);

                            // Cập nhật trạng thái phân trang
                            container.setAttribute('data-page', nextPage);
                            container.setAttribute('data-has-more', data.has_more ? 'true' : 'false');

                            if (counterEl) {
                                const loadedCount = tbody.querySelectorAll('tr').length;
                                counterEl.textContent = loadedCount;
                            }

                            if (!data.has_more && endEl) {
                                endEl.classList.remove('d-none');
                            }
                        } else {
                            container.setAttribute('data-has-more', 'false');
                            if (endEl) endEl.classList.remove('d-none');
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi khi tải thêm danh sách phiếu cấp phát:', err);
                    })
                    .finally(() => {
                        isFetching = false;
                        if (loadingEl) loadingEl.classList.add('d-none');
                    });
            }
        });
    });

    // ==========================================
    // 2. TƯƠNG TÁC CHUYỂN TAB TỪ CÁC THẺ THÁNG
    // ==========================================
    document.querySelectorAll('.month-tab-trigger').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target-tab');
            const triggerBtn = document.querySelector('button[data-bs-target="#' + targetId + '"]');
            if (triggerBtn) {
                const tab = new bootstrap.Tab(triggerBtn);
                tab.show();
                triggerBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js chưa được tải. Vui lòng kiểm tra kết nối mạng hoặc CDN.');
        return;
    }

    const colorPalette = [
        '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6',
        '#06b6d4', '#f97316', '#6366f1', '#14b8a6', '#84cc16',
        '#e11d48', '#0ea5e9', '#64748b', '#d97706'
    ];

    // ==========================================
    // 3. KHỞI TẠO BIỂU ĐỒ HÌNH TRÒN (PHÒNG BAN)
    // ==========================================
    const deptCanvas = document.getElementById('deptPieChart');
    if (deptCanvas) {
        const deptLabels = <?= json_encode($deptLabels, JSON_UNESCAPED_UNICODE) ?>;
        const deptCounts = <?= json_encode($deptCounts) ?>;

        if (deptLabels.length > 0) {
            new Chart(deptCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptCounts,
                        backgroundColor: colorPalette.slice(0, deptLabels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    family: "'Segoe UI', system-ui, sans-serif",
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ' ' + context.label + ': ' + value + ' thiết bị (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '58%'
                }
            });
        }
    }

    // ==========================================
    // 4. KHỞI TẠO BIỂU ĐỒ CỘT (CẤP PHÁT 3 THÁNG)
    // ==========================================
    const monthlyCanvas = document.getElementById('monthlyAllocationChart');
    if (monthlyCanvas) {
        const monthLabels = <?= json_encode($monthLabels, JSON_UNESCAPED_UNICODE) ?>;
        const monthDeviceCounts = <?= json_encode($monthDeviceCounts) ?>;
        const monthTicketCounts = <?= json_encode($monthTicketCounts) ?>;
        const barColors = <?= json_encode($monthBackgroundColors) ?>;

        new Chart(monthlyCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Số thiết bị cấp phát',
                    data: monthDeviceCounts,
                    backgroundColor: barColors,
                    borderRadius: 6,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 11, weight: '500' }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const idx = context.dataIndex;
                                const tickets = monthTicketCounts[idx] || 0;
                                return 'Tổng số phiếu cấp: ' + tickets + ' phiếu';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
