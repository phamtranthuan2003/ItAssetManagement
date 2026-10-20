<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Quản lý Tài Khoản Hệ Thống</h4>
        <div class="text-muted small">Phân quyền quản trị viên (Admin) và Nhân viên kho (Warehouse)</div>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-person-plus me-1"></i> Tạo tài khoản mới
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Vai trò / Quyền hạn</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-end" style="width: 120px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><strong class="font-monospace text-primary">@<?= e($u['username']) ?></strong></td>
                    <td><strong><?= e($u['fullname']) ?></strong></td>
                    <td><?= e($u['email'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= $u['role'] === 'admin' ? 'Quản trị viên (Admin)' : 'Nhân viên kho (Warehouse)' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['status'] == 1): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Bị khóa</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?= formatDate($u['created_at']) ?></small></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $u['id'] ?>" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if ($u['id'] !== Auth::id()): ?>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $u['id'] ?>" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>

                        <!-- Modal Chỉnh sửa -->
                        <div class="modal fade text-start" id="editUserModal<?= $u['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="<?= url('users/' . $u['id'] . '/update') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cập nhật tài khoản [<?= e($u['username']) ?>]</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                                <input type="text" name="fullname" class="form-control" value="<?= e($u['fullname']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= e($u['email'] ?? '') ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Vai trò phân quyền</label>
                                                <select name="role" class="form-select">
                                                    <option value="warehouse" <?= $u['role'] === 'warehouse' ? 'selected' : '' ?>>Nhân viên kho (Warehouse)</option>
                                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên (Admin)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Trạng thái tài khoản</label>
                                                <select name="status" class="form-select">
                                                    <option value="1" <?= $u['status'] == 1 ? 'selected' : '' ?>>Kích hoạt hoạt động</option>
                                                    <option value="0" <?= $u['status'] == 0 ? 'selected' : '' ?>>Khóa tài khoản</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Đặt lại mật khẩu mới (Nếu cần)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi mật khẩu">
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
                        <?php if ($u['id'] !== Auth::id()): ?>
                        <div class="modal fade text-start" id="deleteUserModal<?= $u['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="<?= url('users/' . $u['id'] . '/delete') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa tài khoản</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa tài khoản <strong><?= e($u['username']) ?></strong> (<?= e($u['fullname']) ?>)?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
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
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm người dùng mới -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= url('users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-primary"></i>Tạo tài khoản mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="VD: nam.th, thukho2..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" placeholder="Họ và tên nhân viên" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@company.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Vai trò phân quyền</label>
                        <select name="role" class="form-select">
                            <option value="warehouse" selected>Nhân viên kho (Warehouse)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1" selected>Kích hoạt hoạt động</option>
                            <option value="0">Khóa tài khoản</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>
