<div class="card mb-4">
    <div class="card-body">
        <form action="<?= url('employees') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Phòng ban</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả phòng ban --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $deptId == $d['id'] ? 'selected' : '' ?>>
                            <?= e($d['name']) ?> (<?= e($d['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tình trạng làm việc</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="working" <?= $status === 'working' ? 'selected' : '' ?>>Đang làm việc</option>
                    <option value="resigned" <?= $status === 'resigned' ? 'selected' : '' ?>>Đã nghỉ việc</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="<?= url('employees') ?>" class="btn btn-light btn-sm border">Đặt lại</a>
            </div>

            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createEmpModal">
                    <i class="bi bi-person-plus me-1"></i> Thêm nhân viên
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">STT</th>
                    <th>Họ và tên</th>
                    <th>Phòng ban</th>
                    <th>Chức vụ</th>
                    <th>Liên hệ</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thiết bị đang giữ</th>
                    <th class="text-end" style="width: 110px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Chưa có nhân viên nào phù hợp.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($employees as $e): ?>
                    <tr class="<?= $e['status'] === 'resigned' ? 'table-light text-muted' : '' ?>">
                        <td><?= (int)$e['id'] ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= e($e['fullname']) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                <?= e($e['department_name']) ?>
                            </span>
                        </td>
                        <td><?= e($e['position'] ?? '—') ?></td>
                        <td>
                            <div class="small"><i class="bi bi-telephone me-1"></i><?= e($e['phone'] ?? '—') ?></div>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= e($e['email'] ?? '—') ?></div>
                        </td>
                        <td>
                            <?php if ($e['status'] === 'working'): ?>
                                <span class="badge bg-success">Đang làm việc</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã nghỉ việc</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($e['held_devices_count'] > 0): ?>
                                <a href="<?= url('employees/' . $e['id'] . '?' . http_build_query(['return_to' => $returnTo])) ?>" class="badge <?= $e['status'] === 'resigned' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' ?> border text-decoration-none fs-6">
                                    <i class="bi bi-laptop me-1"></i><?= $e['held_devices_count'] ?> máy
                                    <?php if ($e['status'] === 'resigned'): ?>
                                        <i class="bi bi-exclamation-triangle ms-1" title="Cần thu hồi do nhân viên đã nghỉ việc"></i>
                                    <?php endif; ?>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border fs-6">0 máy</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('employees/' . $e['id'] . '?' . http_build_query(['return_to' => $returnTo])) ?>" class="btn btn-outline-secondary" title="Xem hồ sơ">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($e['held_devices_count'] == 0): ?>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEmpModal<?= $e['id'] ?>" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Modal Xóa -->
                            <?php if ($e['held_devices_count'] == 0): ?>
                            <div class="modal fade text-start" id="deleteEmpModal<?= $e['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?= url('employees/' . $e['id'] . '/delete') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa nhân viên</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Bạn có chắc chắn muốn xóa nhân viên <strong><?= e($e['fullname']) ?></strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-danger">Xóa nhân viên</button>
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
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">Hiển thị <?= count($employees) ?> / <?= number_format($totalEmployees) ?> nhân viên · Trang <?= $page ?> / <?= $totalPages ?></small>
        <nav aria-label="Phân trang nhân viên">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('employees?' . http_build_query(array_merge($filters, ['page' => max(1, $page - 1)]))) ?>">Trước</a>
                </li>
                <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                    <li class="page-item <?= $pageNumber === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('employees?' . http_build_query(array_merge($filters, ['page' => $pageNumber]))) ?>"><?= $pageNumber ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= url('employees?' . http_build_query(array_merge($filters, ['page' => min($totalPages, $page + 1)]))) ?>">Sau</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm mới nhân viên -->
<div class="modal fade" id="createEmpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= url('employees/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-primary"></i>Thêm mới nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">STT</label>
                        <input type="text" class="form-control" value="Tự động" readonly>
                        <div class="form-text">STT sẽ được đánh số tự động trong danh sách sau khi thêm.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập đầy đủ họ tên" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phòng ban <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Chọn phòng ban --</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chức vụ / Vị trí công tác</label>
                        <input type="text" name="position" class="form-control" placeholder="VD: Kế toán viên, Lập trình viên...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="09xxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@company.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tình trạng làm việc</label>
                        <select name="status" class="form-select">
                            <option value="working" selected>Đang làm việc</option>
                            <option value="resigned">Đã nghỉ việc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm nhân viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
