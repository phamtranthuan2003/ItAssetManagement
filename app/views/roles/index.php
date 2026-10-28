<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Quản lý vai trò & quyền (RBAC)</h4>
        <div class="text-muted small">Chọn chính xác màn hình và thao tác mà từng nhóm tài khoản được phép sử dụng.</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="bi bi-plus-lg me-1"></i> Tạo vai trò
    </button>
</div>

<div class="row g-3">
    <?php foreach ($roles as $role): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <div>
                    <strong><?= e($role['name']) ?></strong>
                    <span class="badge bg-secondary ms-2"><?= e($role['code']) ?></span>
                    <?php if ($role['code'] === 'admin'): ?><span class="badge bg-danger ms-1">Super-admin</span><?php endif; ?>
                </div>
                <?php if (!in_array($role['code'], ['admin', 'warehouse', 'customer'], true)): ?>
                <form action="<?= url('roles/' . $role['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Xóa vai trò này?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i> Xóa</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($role['code'] === 'admin'): ?>
                    <p class="text-muted mb-0">Vai trò hệ thống có toàn quyền và không thể thay đổi.</p>
                <?php else: ?>
                <?php if ($role['code'] === 'customer'): ?>
                    <div class="alert alert-info py-2">Role Khách hàng mặc định chưa có quyền nào. Hãy tích quyền cần thiết và lưu trước khi gán role cho tài khoản.</div>
                <?php endif; ?>
                <form action="<?= url('roles/' . $role['id'] . '/permissions') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <?php foreach ($permissionCatalog as $code => $label): ?>
                        <div class="col-md-4 col-lg-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= e($code) ?>" <?= in_array($code, $role['permissions'], true) ? 'checked' : '' ?>>
                                <span class="form-check-label"><?= e($label) ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm mt-3"><i class="bi bi-check-lg me-1"></i> Lưu quyền</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="<?= url('roles/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Tạo vai trò mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã vai trò</label>
                    <input class="form-control" name="code" pattern="[a-z][a-z0-9_]{1,39}" maxlength="40" placeholder="vendor_readonly" required>
                    <div class="form-text">Chữ thường, số và gạch dưới; bắt đầu bằng chữ.</div>
                </div>
                <div>
                    <label class="form-label">Tên hiển thị</label>
                    <input class="form-control" name="name" maxlength="100" placeholder="Đối tác chỉ xem" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo vai trò</button>
            </div>
        </form>
    </div>
</div>
