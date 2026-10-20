<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-1"></i> Thông tin tài khoản
            </div>
            <div class="card-body text-center py-4">
                <div class="display-4 text-primary mb-3">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h5 class="fw-bold"><?= e($user['fullname']) ?></h5>
                <p class="text-muted mb-2">@<?= e($user['username']) ?></p>
                <div>
                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?> px-3 py-2 fs-6">
                        <?= $user['role'] === 'admin' ? 'Quản trị viên (Admin)' : 'Nhân viên kho (Warehouse)' ?>
                    </span>
                </div>
                <div class="mt-4 text-start border-top pt-3">
                    <p class="mb-1"><strong>Email:</strong> <?= e($user['email'] ?: 'Chưa cập nhật') ?></p>
                    <p class="mb-1"><strong>Ngày tạo:</strong> <?= formatDateTime($user['created_at']) ?></p>
                    <p class="mb-0"><strong>Trạng thái:</strong> <span class="badge bg-success">Đang hoạt động</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-lock me-1"></i> Đổi mật khẩu
            </div>
            <div class="card-body p-4">
                <form action="<?= url('profile/password') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required placeholder="Nhập mật khẩu đang dùng">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nhập lại mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Xác nhận lại mật khẩu mới">
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
