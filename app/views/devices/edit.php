<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa thiết bị: <strong><?= e($device['device_code']) ?></strong></span>
                <a href="<?= url('devices/' . $device['id']) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Trở về chi tiết</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('devices/' . $device['id'] . '/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- 1. THÔNG TIN CHUNG -->
                    <h6 class="text-primary fw-bold text-uppercase mb-3 pb-2 border-bottom">
                        <i class="bi bi-info-circle me-1"></i> 1. Thông tin chung
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="device_code" class="form-control" value="<?= e($device['device_code']) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Loại thiết bị <span class="text-danger">*</span></label>
                            <select name="type" id="deviceTypeSelect" class="form-select" required>
                                <option value="Laptop" <?= $device['type'] === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
                                <option value="PC" <?= $device['type'] === 'PC' ? 'selected' : '' ?>>PC (Máy tính để bàn)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hãng sản xuất <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control" value="<?= e($device['brand']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên Model <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control" value="<?= e($device['model']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="<?= e($device['serial_number'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ngày nhập kho <span class="text-danger">*</span></label>
                            <input type="date" name="import_date" class="form-control" value="<?= e($device['import_date']) ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hạn bảo hành</label>
                            <input type="date" name="warranty_expiry" class="form-control" value="<?= e($device['warranty_expiry'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tình trạng máy <span class="text-danger">*</span></label>
                            <select name="condition_state" class="form-select">
                                <option value="new" <?= $device['condition_state'] === 'new' ? 'selected' : '' ?>>Mới 100%</option>
                                <option value="used" <?= $device['condition_state'] === 'used' ? 'selected' : '' ?>>Đang dùng tốt</option>
                                <option value="broken" <?= $device['condition_state'] === 'broken' ? 'selected' : '' ?>>Hỏng hóc</option>
                                <option value="under_warranty" <?= $device['condition_state'] === 'under_warranty' ? 'selected' : '' ?>>Đang bảo hành</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Trạng thái kho <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="in_stock" <?= $device['status'] === 'in_stock' ? 'selected' : '' ?>>Trong kho</option>
                                <option value="allocated" <?= $device['status'] === 'allocated' ? 'selected' : '' ?>>Đang cấp phát</option>
                                <option value="maintenance" <?= $device['status'] === 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                                <option value="liquidated" <?= $device['status'] === 'liquidated' ? 'selected' : '' ?>>Thanh lý</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. CẤU HÌNH PHẦN CỨNG CHI TIẾT -->
                    <h6 class="text-primary fw-bold text-uppercase mb-3 pb-2 border-bottom">
                        <i class="bi bi-cpu me-1"></i> 2. Cấu hình phần cứng chi tiết
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bộ vi xử lý (CPU) <span class="text-danger">*</span></label>
                            <input type="text" name="cpu" class="form-control" value="<?= e($device['cpu'] ?? '') ?>" required>
                        </div>

                        <!-- Mainboard chỉ hiển thị khi chọn PC -->
                        <div class="col-md-6" id="fieldMainboard">
                            <label class="form-label fw-semibold">Mainboard (Bo mạch chủ - Chỉ PC)</label>
                            <input type="text" name="mainboard" class="form-control" value="<?= e($device['mainboard'] ?? '') ?>">
                        </div>

                        <!-- Màn hình chỉ hiển thị khi chọn Laptop -->
                        <div class="col-md-6" id="fieldScreenSize">
                            <label class="form-label fw-semibold">Màn hình (Laptop)</label>
                            <input type="text" name="screen_size" class="form-control" value="<?= e($device['screen_size'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dung lượng RAM (GB) <span class="text-danger">*</span></label>
                            <input type="number" name="ram_gb" class="form-control" value="<?= (int)($device['ram_gb'] ?? 8) ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Loại RAM & Bus</label>
                            <input type="text" name="ram_type" class="form-control" value="<?= e($device['ram_type'] ?? 'DDR4') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ổ cứng SSD</label>
                            <input type="text" name="ssd_capacity" class="form-control" value="<?= e($device['ssd_capacity'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ổ cứng HDD phụ</label>
                            <input type="text" name="hdd_capacity" class="form-control" value="<?= e($device['hdd_capacity'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Card đồ họa (GPU)</label>
                            <input type="text" name="gpu" class="form-control" value="<?= e($device['gpu'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú cấu hình khác</label>
                            <input type="text" name="spec_notes" class="form-control" value="<?= e($device['spec_notes'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- 3. GHI CHÚ BỔ SUNG -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú bổ sung</label>
                        <textarea name="notes" class="form-control" rows="2"><?= e($device['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= url('devices/' . $device['id']) ?>" class="btn btn-secondary px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Cập nhật thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
