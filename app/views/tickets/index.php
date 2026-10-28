<div class="card mb-4">
    <div class="card-body">
        <form action="<?= url('tickets') ?>" method="GET" class="row g-3 align-items-end" data-auto-filter>
            <div class="col-lg-2">
                <label class="form-label small fw-semibold">Loại nghiệp vụ</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">-- Tất cả loại phiếu --</option>
                    <option value="allocation" <?= $type === 'allocation' ? 'selected' : '' ?>>Cấp phát thiết bị</option>
                    <option value="transfer" <?= $type === 'transfer' ? 'selected' : '' ?>>Chuyển giao điều chuyển</option>
                    <option value="revocation" <?= $type === 'revocation' ? 'selected' : '' ?>>Thu hồi về kho</option>
                </select>
            </div>

            <div class="col-lg-2">
                <label class="form-label small fw-semibold">Trạng thái phiếu</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Nháp</option>
                    <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <div class="col-lg-3">
                <label class="form-label small fw-semibold">Người nhận</label>
                <div class="employee-department-select dropdown position-relative" data-employee-search-select>
                    <button type="button" class="form-select form-select-sm text-start" data-employee-select-toggle aria-haspopup="listbox" aria-expanded="false">
                        -- Tất cả người nhận --
                    </button>
                    <select name="to_employee_id" class="d-none" data-employee-select tabindex="-1" aria-hidden="true">
                        <option value="">-- Tất cả người nhận --</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= (int)$employee['id'] ?>" <?= (string)$filters['to_employee_id'] === (string)$employee['id'] ? 'selected' : '' ?>>
                                <?= e($employee['fullname']) ?> (<?= e($employee['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="dropdown-menu w-100 p-2" data-employee-select-menu>
                        <input type="search" class="form-control form-control-sm mb-2" data-employee-search-input aria-label="Tìm người nhận theo tên hoặc mã nhân viên" placeholder="Tìm theo tên hoặc mã nhân viên..." autocomplete="off">
                        <div class="employee-select-options" data-employee-select-options role="listbox"></div>
                        <div class="small text-muted px-2 py-1 d-none" data-employee-no-results>Không tìm thấy người nhận phù hợp.</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <label class="form-label small fw-semibold">Người tạo phiếu</label>
                <select name="created_by" class="form-select form-select-sm">
                    <option value="">-- Tất cả người tạo --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int)$user['id'] ?>" <?= (string)$filters['created_by'] === (string)$user['id'] ? 'selected' : '' ?>>
                            <?= e($user['fullname']) ?> (<?= e($user['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel me-1"></i> Lọc phiếu</button>
                <a href="<?= url('tickets') ?>" class="btn btn-light btn-sm border">Đặt lại</a>
            </div>

            <div class="col-12 text-end">
                <?php if (Auth::hasPermission('tickets.create')): ?>
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
                <?php endif; ?>
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
                    <td colspan="9" class="text-center py-4 text-muted"><?= !empty($filters['to_employee_id']) || !empty($filters['created_by']) || !empty($type) || !empty($status) ? 'Không có kết quả phù hợp với bộ lọc.' : 'Chưa có phiếu nào được tạo.' ?></td>
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
                                <?php if (Auth::hasPermission('tickets.print')): ?>
                                <a href="<?= url('tickets/' . $t['id'] . '/print') ?>" target="_blank" class="btn btn-outline-primary" title="In biên bản">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalTickets > 0): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">
            Hiển thị <?= count($tickets) ?> / <?= number_format($totalTickets) ?> phiếu · Trang <?= $page ?> / <?= max(1, $totalPages) ?>
        </small>
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Phân trang lịch sử cấp phát và thu hồi">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('tickets?' . http_build_query(array_merge($filters, ['page' => max(1, $page - 1)]))) ?>">Trước</a>
                </li>
                <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                    <li class="page-item <?= $pageNumber === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('tickets?' . http_build_query(array_merge($filters, ['page' => $pageNumber]))) ?>"><?= $pageNumber ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('tickets?' . http_build_query(array_merge($filters, ['page' => min($totalPages, $page + 1)]))) ?>">Sau</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
