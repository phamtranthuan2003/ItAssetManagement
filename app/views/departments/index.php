<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Quản lý Phòng Ban</h4>
        <div class="text-muted small">Cơ cấu tổ chức tiếp nhận và phân bổ thiết bị công ty</div>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="bi bi-plus-lg me-1"></i> Thêm mới phòng ban
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 120px;">Mã phòng</th>
                    <th>Tên phòng ban</th>
                    <th>Mô tả chức năng</th>
                    <th class="text-center">Số nhân viên</th>
                    <th class="text-center">Thiết bị đang dùng</th>
                    <th class="text-end" style="width: 130px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departments)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Chưa có phòng ban nào trong hệ thống.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($departments as $d): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border font-monospace fs-6">
                                <?= e($d['code']) ?>
                            </span>
                        </td>
                        <td>
                            <strong class="text-dark"><?= e($d['name']) ?></strong>
                        </td>
                        <td class="text-muted small">
                            <?= e($d['description'] ?? '—') ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info border fs-6">
                                <i class="bi bi-people me-1"></i><?= $d['active_employees_count'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?= url('devices?department_id=' . $d['id']) ?>" class="badge bg-primary-subtle text-primary border text-decoration-none fs-6">
                                <i class="bi bi-laptop me-1"></i><?= $d['allocated_devices_count'] ?> thiết bị
                            </a>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeptModal<?= $d['id'] ?>" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if ($d['active_employees_count'] == 0 && $d['allocated_devices_count'] == 0): ?>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDeptModal<?= $d['id'] ?>" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-outline-secondary disabled" title="Không thể xóa khi có nhân viên hoặc thiết bị">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Modal Chỉnh sửa -->
                            <div class="modal fade text-start" id="editDeptModal<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?= url('departments/' . $d['id'] . '/update') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chỉnh sửa phòng ban</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Mã phòng ban <span class="text-danger">*</span></label>
                                                    <input type="text" name="code" class="form-control" value="<?= e($d['code']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tên phòng ban <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="<?= e($d['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Mô tả</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= e($d['description'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Xóa -->
                            <div class="modal fade text-start" id="deleteDeptModal<?= $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?= url('departments/' . $d['id'] . '/delete') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa phòng ban</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Bạn có chắc chắn muốn xóa phòng ban <strong><?= e($d['name']) ?></strong> (Mã: <?= e($d['code']) ?>)?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm mới phòng ban -->
<div class="modal fade" id="createDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= url('departments/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Thêm mới phòng ban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="VD: PB-IT, PB-KT, PB-HR..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Phòng Công Nghệ Thông Tin..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả chức năng</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Nhiệm vụ, phạm vi hoạt động..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm phòng ban</button>
                </div>
            </form>
        </div>
    </div>
</div>
