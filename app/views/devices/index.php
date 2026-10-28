<div class="card mb-4">
    <div class="card-body">
        <form action="<?= url('devices') ?>" method="GET" class="row g-3" data-auto-filter>
            <!-- Tìm kiếm từ khóa -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Từ khóa tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Loại, hãng, model, serial..." value="<?= e($filters['keyword']) ?>" data-debounce-filter>
                </div>
            </div>

            <!-- Loại thiết bị -->
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Loại thiết bị</label>
                <input type="text" name="type" class="form-control" placeholder="PC, Laptop, Máy CNC..." value="<?= e($filters['type']) ?>" data-debounce-filter>
            </div>

            <!-- Trạng thái -->
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Trạng thái kho</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="in_stock" <?= $filters['status'] === 'in_stock' ? 'selected' : '' ?>>Trong kho</option>
                    <option value="allocated" <?= $filters['status'] === 'allocated' ? 'selected' : '' ?>>Đang cấp phát</option>
                    <option value="maintenance" <?= $filters['status'] === 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                    <option value="liquidated" <?= $filters['status'] === 'liquidated' ? 'selected' : '' ?>>Thanh lý</option>
                </select>
            </div>

            <!-- Tình trạng -->
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Tình trạng máy</label>
                <select name="condition_state" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="new" <?= $filters['condition_state'] === 'new' ? 'selected' : '' ?>>Mới 100%</option>
                    <option value="used" <?= $filters['condition_state'] === 'used' ? 'selected' : '' ?>>Đang dùng tốt</option>
                    <option value="broken" <?= $filters['condition_state'] === 'broken' ? 'selected' : '' ?>>Hỏng hóc</option>
                    <option value="under_warranty" <?= $filters['condition_state'] === 'under_warranty' ? 'selected' : '' ?>>Bảo hành</option>
                </select>
            </div>

            <!-- Phòng ban -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Phòng ban sử dụng</label>
                <select name="department_id" class="form-select">
                    <option value="">-- Tất cả phòng ban --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= $filters['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                            <?= e($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Người đang sử dụng</label>
                <select name="employee_id" class="form-select">
                    <option value="">-- Tất cả người dùng --</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= (int)$employee['id'] ?>" <?= (string)$filters['employee_id'] === (string)$employee['id'] ? 'selected' : '' ?>>
                            <?= e($employee['fullname']) ?> (<?= e($employee['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Lọc theo cấu hình CPU -->
            <div class="col-md-3">
                <input type="text" name="cpu" class="form-control form-control-sm" placeholder="Lọc theo CPU (i5, i7, Ryzen...)" value="<?= e($filters['cpu']) ?>" data-debounce-filter>
            </div>

            <!-- Lọc theo RAM -->
            <div class="col-md-2">
                <select name="ram_gb" class="form-select form-select-sm">
                    <option value="">-- RAM tối thiểu --</option>
                    <option value="8" <?= $filters['ram_gb'] == '8' ? 'selected' : '' ?>>>= 8 GB</option>
                    <option value="16" <?= $filters['ram_gb'] == '16' ? 'selected' : '' ?>>>= 16 GB</option>
                    <option value="32" <?= $filters['ram_gb'] == '32' ? 'selected' : '' ?>>>= 32 GB</option>
                </select>
            </div>

            <!-- Lọc theo SSD -->
            <div class="col-md-2">
                <input type="text" name="ssd_capacity" class="form-control form-control-sm" placeholder="SSD (256GB, 512GB...)" value="<?= e($filters['ssd_capacity']) ?>" data-debounce-filter>
            </div>

            <!-- Checkbox lọc đặc biệt -->
            <div class="col-md-3 d-flex align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_overdue" value="1" id="filterOverdue" <?= !empty($filters['is_overdue']) ? 'checked' : '' ?>>
                    <label class="form-check-label small text-danger fw-bold" for="filterOverdue">
                        Quá hạn trả
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="expiring_warranty" value="1" id="filterExpiring" <?= !empty($filters['expiring_warranty']) ? 'checked' : '' ?>>
                    <label class="form-check-label small text-warning-emphasis fw-bold" for="filterExpiring">
                        Sắp hết BH
                    </label>
                </div>
            </div>

            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="<?= url('devices') ?>" class="btn btn-light btn-sm text-secondary border">Đặt lại</a>
            </div>
        </form>
    </div>
</div>

<!-- THANH CÔNG CỤ: THÊM MỚI & XUẤT EXCEL -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">
        Tìm thấy <strong><?= number_format($totalDevices) ?></strong> thiết bị phù hợp.
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('devices/export?' . http_build_query($filters)) ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel / CSV
        </a>
        <a href="<?= url('devices/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm mới thiết bị
        </a>
    </div>
</div>

<!-- BẢNG DANH SÁCH THIẾT BỊ -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">STT</th>
                    <th>Loại thiết bị</th>
                    <th>Cấu hình chi tiết</th>
                    <th>Trạng thái</th>
                    <th>Tình trạng</th>
                    <th>Người dùng / Phòng ban</th>
                    <th>Hạn BH</th>
                    <th class="text-end" style="width: 130px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devices)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Không tìm thấy thiết bị nào phù hợp với điều kiện tìm kiếm.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($devices as $d): ?>
                    <tr>
                        <td>
                            <a href="<?= url('devices/' . $d['id']) ?>" class="fw-bold text-decoration-none">
                                <?= (int)$d['id'] ?>
                            </a>
                            <div class="small text-muted font-monospace">SN: <?= e($d['serial_number'] ?? '—') ?></div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="bi bi-<?= $d['type'] === 'PC' ? 'pc' : ($d['type'] === 'Laptop' ? 'laptop' : 'box-seam') ?> me-1"></i><?= e($d['type']) ?>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark"><?= e($d['model'] ?: '—') ?></div>
                                    <?php if (!empty($d['brand'])): ?>
                                        <small class="text-muted"><?= e($d['brand']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-muted">
                                <?php if (!empty($d['cpu'])): ?>
                                    <span><?= e($d['cpu']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($d['ram_gb'])): ?>
                                    <span><?= !empty($d['cpu']) ? ' | ' : '' ?>RAM <?= e($d['ram_gb']) ?>GB <?= e($d['ram_type'] ?? '') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($d['ssd_capacity'])): ?>
                                    <span><?= (!empty($d['cpu']) || !empty($d['ram_gb'])) ? ' | ' : '' ?>SSD <?= e($d['ssd_capacity']) ?></span>
                                <?php endif; ?>
                                <?php if (empty($d['cpu']) && empty($d['ram_gb']) && empty($d['ssd_capacity'])): ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= renderStatusBadge($d['status']) ?></td>
                        <td><?= renderConditionBadge($d['condition_state']) ?></td>
                        <td>
                            <?php if (!empty($d['employee_name'])): ?>
                                <div class="fw-semibold text-dark">
                                    <a href="<?= url('employees/' . $d['current_employee_id']) ?>" class="text-decoration-none">
                                        <?= e($d['employee_name']) ?>
                                    </a>
                                </div>
                                <div class="small text-muted"><?= e($d['department_name']) ?></div>
                                <?php if ($d['is_temporary']): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small mt-1">
                                        <i class="bi bi-clock me-1"></i>Mượn tạm đến <?= formatDate($d['expected_return_date']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-house-door me-1"></i>Tại kho</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($d['warranty_expiry'])): ?>
                                <small class="text-muted"><?= formatDate($d['warranty_expiry']) ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('devices/' . $d['id'] . '?' . http_build_query(['return_to' => $returnTo])) ?>" class="btn btn-outline-secondary" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= url('devices/' . $d['id'] . '/edit?' . http_build_query(['return_to' => $returnTo])) ?>" class="btn btn-outline-primary" title="Sửa thông tin">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if (Auth::isAdmin()): ?>
                                <button type="button" class="btn btn-outline-danger" title="Xóa" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $d['id'] ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Modal xác nhận xóa -->
                            <?php if (Auth::isAdmin()): ?>
                            <div class="modal fade text-start" id="deleteModal<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?= url('devices/' . $d['id'] . '/delete') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa thiết bị</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Bạn có chắc chắn muốn xóa thiết bị STT <strong><?= (int)$d['id'] ?></strong> (<?= e($d['model'] ?: $d['type']) ?>) khỏi hệ thống? Các lịch sử và chi tiết phiếu liên quan cũng sẽ bị xóa. Thao tác này không thể hoàn tác.
                                                <?php if (!empty($d['current_employee_id'])): ?>
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" name="delete_employee" value="1" id="deleteEmployee<?= (int)$d['id'] ?>">
                                                    <label class="form-check-label" for="deleteEmployee<?= (int)$d['id'] ?>">
                                                        Xóa luôn nhân viên <strong><?= e($d['employee_name'] ?? '') ?></strong>
                                                    </label>
                                                    <div class="form-text">
                                                        Không tích chọn: chỉ xóa thiết bị này. Nếu nhân viên còn thiết bị khác, hệ thống sẽ yêu cầu xác nhận thêm trước khi xóa nhân viên và đưa các thiết bị đó về kho.
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                                                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">Trang <?= $page ?> / <?= $totalPages ?></small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('devices?' . http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>">Trước</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('devices?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('devices?' . http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>">Sau</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
