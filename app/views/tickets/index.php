<div class="card mb-4">
    <div class="card-body">
        <form action="<?= url('tickets') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Loại nghiệp vụ</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">-- Tất cả loại phiếu --</option>
                    <option value="allocation" <?= $type === 'allocation' ? 'selected' : '' ?>>Cấp phát thiết bị</option>
                    <option value="transfer" <?= $type === 'transfer' ? 'selected' : '' ?>>Chuyển giao điều chuyển</option>
                    <option value="revocation" <?= $type === 'revocation' ? 'selected' : '' ?>>Thu hồi về kho</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Trạng thái phiếu</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Nháp</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel me-1"></i> Lọc phiếu</button>
                <a href="<?= url('tickets') ?>" class="btn btn-light btn-sm border">Đặt lại</a>
            </div>

            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-lg me-1"></i> Lập phiếu mới
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="<?= url('tickets/create?type=allocation') ?>"><i class="bi bi-box-arrow-right text-primary me-2"></i> Phiếu cấp phát</a></li>
                        <li><a class="dropdown-item" href="<?= url('tickets/create?type=transfer') ?>"><i class="bi bi-arrow-left-right text-info me-2"></i> Phiếu chuyển giao</a></li>
                        <li><a class="dropdown-item" href="<?= url('tickets/create?type=revocation') ?>"><i class="bi bi-box-arrow-in-left text-danger me-2"></i> Phiếu thu hồi</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 150px;">Mã phiếu</th>
                    <th>Loại phiếu</th>
                    <th>Ngày lập</th>
                    <th>Bên giao / Bàn giao</th>
                    <th>Bên nhận / Tiếp quản</th>
                    <th class="text-center">Số thiết bị</th>
                    <th>Trạng thái</th>
                    <th>Người tạo</th>
                    <th class="text-end" style="width: 130px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Chưa có phiếu nào được tạo.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td>
                            <a href="<?= url('tickets/' . $t['id']) ?>" class="fw-bold text-decoration-none font-monospace">
                                <?= e($t['ticket_code']) ?>
                            </a>
                        </td>
                        <td><?= renderTicketTypeBadge($t['ticket_type']) ?></td>
                        <td><?= formatDate($t['ticket_date']) ?></td>
                        <td>
                            <?php if (!empty($t['from_employee_name'])): ?>
                                <strong><?= e($t['from_employee_name']) ?></strong>
                                <div class="small text-muted"><?= e($t['from_department_name']) ?></div>
                            <?php else: ?>
                                <span class="text-muted"><i class="bi bi-box-seam me-1"></i>Kho công ty</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($t['to_employee_name'])): ?>
                                <strong><?= e($t['to_employee_name']) ?></strong>
                                <div class="small text-muted"><?= e($t['to_department_name']) ?></div>
                                <?php if ($t['is_temporary']): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small mt-1">
                                        Mượn tạm đến <?= formatDate($t['expected_return_date']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted"><i class="bi bi-box-seam me-1"></i>Nhập lại kho</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border fs-6">
                                <?= $t['device_count'] ?> thiết bị
                            </span>
                        </td>
                        <td><?= renderTicketStatusBadge($t['status']) ?></td>
                        <td><small class="text-muted"><?= e($t['creator_name']) ?></small></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('tickets/' . $t['id']) ?>" class="btn btn-outline-secondary" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= url('tickets/' . $t['id'] . '/print') ?>" target="_blank" class="btn btn-outline-primary" title="In biên bản">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
