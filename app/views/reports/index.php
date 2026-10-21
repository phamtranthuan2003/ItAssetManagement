<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Báo Cáo & Thống Kê Kho Thiết Bị</h4>
        <div class="text-muted small">Số liệu tổng hợp hiện trạng tài sản PC, Laptop trong toàn công ty</div>
    </div>
    <div>
        <a href="<?= url('devices/export') ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Xuất toàn bộ dữ liệu ra Excel / CSV
        </a>
    </div>
</div>

<!-- THẺ CHỈ SỐ -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-start border-primary border-4">
            <span class="text-muted small">Tổng tài sản thiết bị</span>
            <h3 class="fw-bold text-dark mb-0"><?= $stats['total'] ?></h3>
            <div class="small text-muted mt-1">PC: <?= $stats['pc_count'] ?> &bull; Laptop: <?= $stats['laptop_count'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-success border-4">
            <span class="text-muted small">Thiết bị sẵn sàng trong kho</span>
            <h3 class="fw-bold text-success mb-0"><?= $stats['in_stock'] ?></h3>
            <div class="small text-muted mt-1"><?= round(($stats['in_stock'] / max(1, $stats['total'])) * 100) ?>% tổng kho</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-info border-4">
            <span class="text-muted small">Thiết bị đang khai thác</span>
            <h3 class="fw-bold text-primary mb-0"><?= $stats['allocated'] ?></h3>
            <div class="small text-muted mt-1"><?= round(($stats['allocated'] / max(1, $stats['total'])) * 100) ?>% tổng kho</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-warning border-4">
            <span class="text-muted small">Cần bảo dưỡng / Xử lý</span>
            <h3 class="fw-bold text-warning mb-0"><?= $stats['maintenance'] ?></h3>
            <div class="small text-muted mt-1">Thanh lý: <?= $stats['liquidated'] ?></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- BÁO CÁO PHÂN BỔ PHÒNG BAN -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pie-chart me-2 text-primary"></i>Phân bổ thiết bị theo Phòng Ban</span>
                <a href="<?= url('devices/export') ?>" class="btn btn-sm btn-outline-secondary">Xuất CSV</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Phòng ban</th>
                            <th class="text-center">Số thiết bị</th>
                            <th class="text-end">Tỷ lệ sử dụng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalAlloc = max(1, $stats['allocated']);
                        foreach ($deptStats as $dept): 
                            $ratio = round(($dept['device_count'] / $totalAlloc) * 100);
                        ?>
                        <tr>
                            <td>
                                <strong><?= e($dept['department_name']) ?></strong>
                                <div class="small text-muted font-monospace"><?= e($dept['department_code']) ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6"><?= $dept['device_count'] ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; width: 100px;">
                                        <div class="progress-bar bg-primary" style="width: <?= $ratio ?>%"></div>
                                    </div>
                                    <span class="small fw-semibold"><?= $ratio ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH CẤP TẠM QUÁ HẠN -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="text-danger fw-bold"><i class="bi bi-clock-history me-2"></i>Thiết bị mượn tạm QUÁ HẠN</span>
                <span class="badge bg-danger"><?= count($overdueDevices) ?> thiết bị</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdueDevices)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check2-circle fs-3 text-success d-block mb-1"></i>
                        Không có thiết bị mượn tạm nào bị quá hạn.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Người mượn</th>
                                    <th>Hạn trả</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueDevices as $ov): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('devices/' . $ov['id']) ?>" class="fw-bold text-decoration-none">
                                            <?= (int)$ov['id'] ?>
                                        </a>
                                        <div class="small text-muted"><?= e($ov['model']) ?></div>
                                    </td>
                                    <td>
                                        <strong><?= e($ov['employee_name']) ?></strong>
                                        <div class="small text-muted"><?= e($ov['department_name']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger"><?= formatDate($ov['expected_return_date']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= url('tickets/create?type=revocation') ?>" class="btn btn-outline-danger btn-sm">
                                            Thu hồi
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- THIẾT BỊ SẮP HẾT HẠN BẢO HÀNH (30 NGÀY TỚI) -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="text-warning-emphasis fw-bold"><i class="bi bi-shield-exclamation me-2 text-warning"></i>Thiết bị sắp hết hạn bảo hành (trong 30 ngày)</span>
        <span class="badge bg-warning text-dark"><?= count($expiringDevices) ?> thiết bị</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Loại</th>
                    <th>Thương hiệu & Model</th>
                    <th>Số Serial</th>
                    <th>Ngày hết hạn BH</th>
                    <th>Số ngày còn lại</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expiringDevices)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        Không có thiết bị nào sắp hết hạn bảo hành trong 30 ngày tới.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($expiringDevices as $ed): ?>
                    <tr>
                        <td>
                            <a href="<?= url('devices/' . $ed['id']) ?>" class="fw-bold text-decoration-none">
                                <?= (int)$ed['id'] ?>
                            </a>
                        </td>
                        <td><?= e($ed['type']) ?></td>
                        <td><strong><?= e($ed['brand']) ?> <?= e($ed['model']) ?></strong></td>
                        <td class="font-monospace"><?= e($ed['serial_number'] ?? '—') ?></td>
                        <td><span class="badge bg-warning text-dark"><?= formatDate($ed['warranty_expiry']) ?></span></td>
                        <td><strong class="text-danger">Còn <?= $ed['days_left'] ?> ngày</strong></td>
                        <td><?= renderStatusBadge($ed['status']) ?></td>
                        <td class="text-end">
                            <a href="<?= url('devices/' . $ed['id']) ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
