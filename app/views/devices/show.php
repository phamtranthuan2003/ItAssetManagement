<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>STT <?= (int)$device['id'] ?></span>
            <?= renderStatusBadge($device['status']) ?>
            <?= renderConditionBadge($device['condition_state']) ?>
        </h4>
        <div class="text-muted small">
            <?= e($device['type']) ?> &bull; <?= e($device['brand']) ?> <?= e($device['model']) ?> &bull; Serial: <code><?= e($device['serial_number'] ?? 'Chưa có') ?></code>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if (Auth::hasPermission('devices.update')): ?>
        <a href="<?= url('devices/' . $device['id'] . '/edit?' . http_build_query(['return_to' => $returnTo])) ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
        </a>
        <?php endif; ?>
        <a href="<?= url($returnTo) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Danh sách
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- CỘT TRÁI: CẤU HÌNH PHẦN CỨNG CHI TIẾT -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white">
                <i class="bi bi-motherboard me-2 text-primary"></i>Thông số kỹ thuật & Cấu hình
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Bộ vi xử lý (CPU)</span>
                            <strong class="text-dark"><i class="bi bi-cpu me-1 text-primary"></i><?= e($device['cpu'] ?? '—') ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($device['mainboard'])): ?>
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Bo mạch chủ (Mainboard)</span>
                            <strong class="text-dark"><i class="bi bi-grid-3x3 me-1 text-info"></i><?= e($device['mainboard']) ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($device['screen_size'])): ?>
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Màn hình tích hợp</span>
                            <strong class="text-dark"><i class="bi bi-display me-1 text-primary"></i><?= e($device['screen_size']) ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php foreach (($device['monitors'] ?? []) as $index => $monitor): ?>
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Màn hình đi kèm #<?= $index + 1 ?></span>
                            <strong class="text-dark">
                                <i class="bi bi-display me-1 text-primary"></i>
                                <?= e($monitor['brand']) ?>
                                <?= !empty($monitor['model']) ? ' ' . e($monitor['model']) : '' ?>
                                <?= !empty($monitor['size']) ? ' · ' . e($monitor['size']) . ' inch' : '' ?>
                            </strong>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Bộ nhớ RAM</span>
                            <strong class="text-dark"><i class="bi bi-memory me-1 text-success"></i><?= !empty($device['ram_gb']) ? e($device['ram_gb']) . ' GB' : '—' ?><?= !empty($device['ram_type']) ? ' (' . e($device['ram_type']) . ')' : '' ?></strong>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Ổ cứng thể rắn (SSD)</span>
                            <strong class="text-dark"><i class="bi bi-hdd me-1 text-warning"></i><?= e($device['ssd_capacity'] ?? '—') ?></strong>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Ổ cứng cơ (HDD)</span>
                            <strong class="text-dark"><?= e($device['hdd_capacity'] ?? '—') ?></strong>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Card đồ họa (GPU)</span>
                            <strong class="text-dark"><i class="bi bi-gpu-card me-1 text-danger"></i><?= e($device['gpu'] ?? '—') ?></strong>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Ngày nhập kho</span>
                            <strong class="text-dark"><?= formatDate($device['import_date']) ?></strong>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Hạn bảo hành</span>
                            <?php 
                                $isExpiringSoon = !empty($device['warranty_expiry']) && (strtotime($device['warranty_expiry']) - time() <= 30*86400) && (strtotime($device['warranty_expiry']) >= time());
                            ?>
                            <strong class="<?= $isExpiringSoon ? 'text-danger' : 'text-dark' ?>">
                                <?= formatDate($device['warranty_expiry']) ?>
                                <?php if ($isExpiringSoon): ?>
                                    <span class="badge bg-danger ms-1">Sắp hết hạn</span>
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>

                    <?php if (!empty($device['notes'])): ?>
                    <div class="col-12">
                        <div class="p-2 bg-light rounded">
                            <span class="text-muted small d-block">Ghi chú thiết bị</span>
                            <div class="small text-secondary"><?= nl2br(e($device['notes'])) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: HIỆN TRẠNG & ĐIỀU CHUYỂN -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white">
                <i class="bi bi-geo-alt me-2 text-primary"></i>Hiện trạng & Vị trí sử dụng
            </div>
            <div class="card-body">
                <?php if ($device['status'] === 'allocated'): ?>
                    <div class="alert alert-primary py-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-person-circle fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold"><?= e($device['employee_name']) ?> (<?= e($device['employee_code']) ?>)</h6>
                                <small class="text-muted"><?= e($device['department_name']) ?></small>
                            </div>
                        </div>
                        <div class="small text-secondary border-top pt-2 mt-2">
                            <div><i class="bi bi-telephone me-1"></i>SĐT: <?= e($device['employee_phone'] ?? '—') ?></div>
                            <div><i class="bi bi-envelope me-1"></i>Email: <?= e($device['employee_email'] ?? '—') ?></div>
                            <?php if ($device['is_temporary']): ?>
                                <div class="mt-2 text-danger fw-bold">
                                    <i class="bi bi-alarm me-1"></i>Cấp phát tạm thời đến: <?= formatDate($device['expected_return_date']) ?>
                                    <?php if (strtotime($device['expected_return_date']) < time()): ?>
                                        <span class="badge bg-danger">ĐÃ QUÁ HẠN</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Nút thao tác nhanh khi đang cấp phát -->
                    <div class="d-grid gap-2">
                        <?php if (Auth::hasPermission('tickets.create')): ?>
                        <a href="<?= url('tickets/create?type=transfer') ?>" class="btn btn-outline-info">
                            <i class="bi bi-arrow-left-right me-1"></i> Chuyển giao sang người khác
                        </a>
                        <?php endif; ?>
                        <?php if (Auth::hasPermission('tickets.create')): ?>
                        <a href="<?= url('tickets/create?type=revocation') ?>" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-in-left me-1"></i> Lập phiếu thu hồi về kho
                        </a>
                        <?php endif; ?>
                    </div>

                <?php elseif ($device['status'] === 'in_stock'): ?>
                    <div class="alert alert-success py-3 mb-3 text-center">
                        <i class="bi bi-box-seam fs-1 text-success d-block mb-2"></i>
                        <h6 class="fw-bold text-success mb-1">Thiết bị đang nằm trong kho</h6>
                        <small class="text-muted">Sẵn sàng để cấp phát cho nhân viên hoặc phòng ban có nhu cầu</small>
                    </div>

                    <div class="d-grid gap-2">
                        <?php if (Auth::hasPermission('tickets.create')): ?>
                        <a href="<?= url('tickets/create?type=allocation') ?>" class="btn btn-success">
                            <i class="bi bi-box-arrow-right me-1"></i> Cấp phát thiết bị này ngay
                        </a>
                        <?php endif; ?>
                        <?php if (Auth::hasPermission('maintenance.create')): ?>
                        <a href="<?= url('maintenance/create?device_id=' . $device['id']) ?>" class="btn btn-outline-warning text-dark">
                            <i class="bi bi-tools me-1"></i> Gửi bảo dưỡng / Nâng cấp RAM/SSD
                        </a>
                        <?php endif; ?>
                    </div>

                <?php elseif ($device['status'] === 'maintenance'): ?>
                    <div class="alert alert-warning py-3 mb-3 text-center">
                        <i class="bi bi-tools fs-1 text-warning d-block mb-2"></i>
                        <h6 class="fw-bold text-warning mb-1">Thiết bị đang bảo trì / sửa chữa</h6>
                        <small class="text-muted">Tạm ngưng cấp phát cho đến khi hoàn thành sửa chữa</small>
                    </div>
                    <div class="d-grid">
                        <?php if (Auth::hasPermission('maintenance.view')): ?>
                        <a href="<?= url('maintenance') ?>" class="btn btn-outline-warning text-dark">
                            <i class="bi bi-eye me-1"></i> Xem danh sách bảo trì
                        </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary py-3 mb-3 text-center">
                        <i class="bi bi-archive fs-1 text-secondary d-block mb-2"></i>
                        <h6 class="fw-bold mb-1">Thiết bị đã thanh lý</h6>
                        <small class="text-muted">Đã ngưng sử dụng và loại bỏ khỏi tài sản đang khai thác</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- LỊCH SỬ DÒNG THỜI GIAN (AUDIT TRAIL & TIMELINE) -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Nhật ký vòng đời & Lịch sử cấp phát thiết bị</span>
        <span class="badge bg-light text-dark border"><?= count($timeline) ?> bản ghi</span>
    </div>
    <div class="card-body">
        <?php if (empty($timeline)): ?>
            <p class="text-muted text-center py-3">Chưa có lịch sử thao tác nào cho thiết bị này.</p>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($timeline as $item): ?>
                <div class="timeline-item">
                    <span class="timeline-dot"></span>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <div>
                            <strong class="text-dark"><?= e($item['performer_name']) ?></strong>
                            <?php if (!empty($item['ticket_code'])): ?>
                                &bull; <a href="<?= url('tickets/' . $item['ticket_id']) ?>" class="badge bg-primary-subtle text-primary text-decoration-none">
                                    Phiếu: <?= e($item['ticket_code']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= formatDateTime($item['action_date']) ?></small>
                    </div>
                    <div class="mt-1 text-secondary"><?= e($item['description']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- LỊCH SỬ BẢO DƯỠNG & SỬA CHỮA / NÂNG CẤP -->
<?php if (!empty($maintenances)): ?>
<div class="card mb-4">
    <div class="card-header bg-white">
        <i class="bi bi-wrench-adjustable me-2 text-primary"></i>Lịch sử bảo dưỡng, sửa chữa & nâng cấp
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Loại</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày hoàn thành</th>
                    <th>Chi phí</th>
                    <th>Đơn vị thực hiện</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maintenances as $m): ?>
                <tr>
                    <td>
                        <span class="badge <?= $m['maintenance_type'] === 'upgrade' ? 'bg-info' : 'bg-warning text-dark' ?>">
                            <?= match ($m['maintenance_type']) {
                                'upgrade' => 'Nâng cấp',
                                'repair'  => 'Sửa chữa',
                                default   => 'Bảo dưỡng'
                            } ?>
                        </span>
                    </td>
                    <td><?= formatDate($m['start_date']) ?></td>
                    <td><?= formatDate($m['end_date']) ?></td>
                    <td><strong><?= formatMoney($m['cost']) ?></strong></td>
                    <td><?= e($m['provider'] ?? 'Nội bộ') ?></td>
                    <td><?= e($m['description']) ?></td>
                    <td>
                        <?php if ($m['status'] === 'completed'): ?>
                            <span class="badge bg-success">Đã hoàn thành</span>
                        <?php elseif ($m['status'] === 'in_progress'): ?>
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Không sửa được</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
