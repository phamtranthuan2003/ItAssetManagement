<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 font-monospace">
            <?= e($ticket['ticket_code']) ?>
            <?= renderTicketTypeBadge($ticket['ticket_type']) ?>
            <?= renderTicketStatusBadge($ticket['status']) ?>
        </h4>
        <div class="text-muted small">
            Ngày lập: <?= formatDate($ticket['ticket_date']) ?> &bull; Người lập phiếu: <strong><?= e($ticket['creator_name']) ?></strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if (Auth::hasPermission('tickets.print')): ?>
        <a href="<?= url('tickets/' . $ticket['id'] . '/print') ?>" target="_blank" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> In biên bản bàn giao (PDF/HTML)
        </a>
        <?php endif; ?>
        <a href="<?= url('tickets') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Danh sách phiếu
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- THÔNG TIN BÊN GIAO -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white">
                <i class="bi bi-box-arrow-right me-2 text-danger"></i>Đơn vị / Người bàn giao
            </div>
            <div class="card-body">
                <?php if (!empty($ticket['from_employee_name'])): ?>
                    <h5 class="fw-bold mb-1"><?= e($ticket['from_employee_name']) ?></h5>
                    <div class="text-muted small mb-2">Mã NV: <code><?= e($ticket['from_employee_code']) ?></code></div>
                    <div class="small"><strong>Phòng ban:</strong> <?= e($ticket['from_department_name']) ?></div>
                    <div class="small"><strong>Chức vụ:</strong> <?= e($ticket['from_employee_pos'] ?? '—') ?></div>
                <?php else: ?>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building fs-3 text-secondary"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Kho Thiết Bị Công Ty</h5>
                            <small class="text-muted">Xuất từ kho trung tâm</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- THÔNG TIN BÊN TIẾP NHẬN -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white">
                <i class="bi bi-box-arrow-in-right me-2 text-success"></i>Đơn vị / Người tiếp nhận
            </div>
            <div class="card-body">
                <?php if (!empty($ticket['to_employee_name'])): ?>
                    <h5 class="fw-bold mb-1"><?= e($ticket['to_employee_name']) ?></h5>
                    <div class="text-muted small mb-2">Mã NV: <code><?= e($ticket['to_employee_code']) ?></code></div>
                    <div class="small"><strong>Phòng ban:</strong> <?= e($ticket['to_department_name']) ?></div>
                    <div class="small"><strong>Chức vụ:</strong> <?= e($ticket['to_employee_pos'] ?? '—') ?></div>
                    <?php if ($ticket['is_temporary']): ?>
                        <div class="mt-2 text-danger fw-bold small">
                            <i class="bi bi-alarm me-1"></i>Cấp phát mượn tạm &bull; Hạn trả: <?= formatDate($ticket['expected_return_date']) ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam fs-3 text-success"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Kho Thiết Bị Công Ty</h5>
                            <small class="text-muted">Nhập lại vào kho quản lý</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- LÝ DO VÀ GHI CHÚ -->
<?php if (!empty($ticket['reason']) || !empty($ticket['notes'])): ?>
<div class="card mb-4">
    <div class="card-body">
        <?php if (!empty($ticket['reason'])): ?>
            <p class="mb-1"><strong>Lý do thực hiện:</strong> <?= e($ticket['reason']) ?></p>
        <?php endif; ?>
        <?php if (!empty($ticket['notes'])): ?>
            <p class="mb-0 text-muted"><strong>Ghi chú:</strong> <?= e($ticket['notes']) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- DANH SÁCH THIẾT BỊ BÀN GIAO TRONG PHIẾU -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-2 text-primary"></i>Danh mục thiết bị & Phụ kiện bàn giao</span>
        <span class="badge bg-primary"><?= count($details) ?> thiết bị</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th>STT thiết bị</th>
                    <th>Loại & Tên Model</th>
                    <th>Cấu hình tóm tắt</th>
                    <th>Phụ kiện đi kèm bàn giao</th>
                    <?php if ($ticket['ticket_type'] === 'revocation'): ?>
                    <th>Tình trạng lúc thu hồi</th>
                    <?php endif; ?>
                    <th class="text-end">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 1;
                foreach ($details as $item): 
                ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td>
                        <a href="<?= url('devices/' . $item['device_id']) ?>" class="fw-bold text-decoration-none">
                            <?= (int)$item['device_id'] ?>
                        </a>
                        <div class="small text-muted font-monospace">SN: <?= e($item['serial_number'] ?? '—') ?></div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border me-1"><?= e($item['type']) ?></span>
                        <strong><?= e($item['brand']) ?> <?= e($item['model']) ?></strong>
                    </td>
                    <td>
                        <small class="text-dark d-block"><?= e($item['cpu']) ?></small>
                        <small class="text-muted">RAM <?= e($item['ram_gb']) ?>GB | SSD <?= e($item['ssd_capacity'] ?? '—') ?></small>
                    </td>
                    <td>
                        <?= !empty($item['accessories']) ? e($item['accessories']) : '<span class="text-muted">Không có</span>' ?>
                    </td>
                    <?php if ($ticket['ticket_type'] === 'revocation'): ?>
                    <td>
                        <?php if ($item['returned_condition'] === 'good'): ?>
                            <span class="badge bg-success">Tốt (Về kho)</span>
                        <?php elseif ($item['returned_condition'] === 'broken'): ?>
                            <span class="badge bg-danger">Hỏng hóc (Bảo trì)</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Thiếu phụ kiện</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-end">
                        <a href="<?= url('devices/' . $item['device_id']) ?>" class="btn btn-outline-secondary btn-sm" title="Xem thiết bị">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
