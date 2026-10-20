<!-- CẢNH BÁO NẾU CÓ THIẾT BỊ QUÁ HẠN TRẢ HOẶC SẮP HẾT HẠN BẢO HÀNH -->
<?php if (!empty($overdueDevices)): ?>
<div class="alert alert-danger shadow-sm border-danger d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-clock-history fs-3 text-danger me-3"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Cảnh báo: Có <?= count($overdueDevices) ?> thiết bị mượn tạm đã QUÁ HẠN TRẢ!</h6>
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
        <div class="card stat-card bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Tổng thiết bị</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total']) ?></h3>
                    <div class="small text-muted mt-1">
                        <i class="bi bi-pc me-1"></i>PC: <strong><?= $stats['pc_count'] ?></strong> | 
                        <i class="bi bi-laptop me-1"></i>Laptop: <strong><?= $stats['laptop_count'] ?></strong>
                    </div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Trong kho -->
    <div class="col-xl-3 col-sm-6">
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
    </div>

    <!-- Đang cấp phát -->
    <div class="col-xl-3 col-sm-6">
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
    </div>

    <!-- Đang bảo trì -->
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card bg-white p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Đang bảo trì / Sửa</span>
                    <h3 class="fw-bold mb-0 text-warning"><?= number_format($stats['maintenance']) ?></h3>
                    <div class="small text-muted mt-1">Thanh lý: <?= $stats['liquidated'] ?> máy</div>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
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
            <a href="<?= url('devices/create') ?>" class="btn btn-light fw-semibold">
                <i class="bi bi-plus-circle text-primary me-1"></i> Nhập thiết bị mới
            </a>
            <a href="<?= url('tickets/create?type=allocation') ?>" class="btn btn-success fw-semibold">
                <i class="bi bi-box-arrow-right me-1"></i> Lập phiếu cấp phát
            </a>
            <a href="<?= url('tickets/create?type=transfer') ?>" class="btn btn-info text-white fw-semibold">
                <i class="bi bi-arrow-left-right me-1"></i> Chuyển giao thiết bị
            </a>
            <a href="<?= url('tickets/create?type=revocation') ?>" class="btn btn-danger fw-semibold">
                <i class="bi bi-box-arrow-in-left me-1"></i> Lập phiếu thu hồi
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: THỐNG KÊ PHÒNG BAN -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-buildings me-2 text-primary"></i>Thiết bị theo phòng ban</span>
                <a href="<?= url('departments') ?>" class="btn btn-sm btn-outline-primary py-0">Chi tiết</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng ban</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Tỷ lệ</th>
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
                                    <div class="fw-semibold"><?= e($dept['department_name']) ?></div>
                                    <small class="text-muted"><?= e($dept['department_code']) ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fs-6"><?= $dept['device_count'] ?></span>
                                </td>
                                <td class="text-end" style="width: 140px;">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= $percent ?>%</small>
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

    <!-- CỘT PHẢI: NHẬT KÝ THAO TÁC GẦN ĐÂY -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-activity me-2 text-primary"></i>Hoạt động kho gần đây</span>
                <span class="badge bg-light text-dark border">Cập nhật thời gian thực</span>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (empty($recentActivities)): ?>
                        <p class="text-muted text-center py-3">Chưa có hoạt động nào được ghi nhận.</p>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $act): ?>
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <div class="d-flex justify-content-between align-items-baseline">
                                <span class="fw-semibold text-dark"><?= e($act['performer_name']) ?></span>
                                <small class="text-muted"><?= formatDateTime($act['action_date']) ?></small>
                            </div>
                            <div class="small text-secondary mt-1"><?= e($act['description']) ?></div>
                            <div class="mt-1">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-tag me-1"></i><?= e($act['device_code']) ?> (<?= e($act['model']) ?>)
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
