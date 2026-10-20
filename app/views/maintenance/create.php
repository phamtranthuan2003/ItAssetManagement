<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-tools me-2 text-warning"></i>Tạo phiếu bảo trì / Sửa chữa / Nâng cấp</span>
                <a href="<?= url('maintenance') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Trở về</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('maintenance/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn thiết bị <span class="text-danger">*</span></label>
                        <select name="device_id" class="form-select" required>
                            <option value="">-- Chọn thiết bị cần bảo trì / nâng cấp --</option>
                            <?php foreach ($devices as $dev): ?>
                                <option value="<?= $dev['id'] ?>" <?= $defaultDeviceId == $dev['id'] ? 'selected' : '' ?>>
                                    [<?= e($dev['device_code']) ?>] <?= e($dev['type']) ?> <?= e($dev['brand']) ?> <?= e($dev['model']) ?> (Hiện trạng: <?= e($dev['status']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại hình thực hiện <span class="text-danger">*</span></label>
                            <select name="maintenance_type" class="form-select" required>
                                <option value="repair" selected>Sửa chữa hỏng hóc (Bảo hành)</option>
                                <option value="upgrade">Nâng cấp linh kiện (Thêm RAM, SSD...)</option>
                                <option value="routine">Bảo trì định kỳ / Vệ sinh máy</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chi phí dự kiến (VNĐ)</label>
                            <input type="number" name="cost" class="form-control" value="0" min="0" placeholder="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn vị tiếp nhận sửa chữa / Bảo hành</label>
                            <input type="text" name="provider" class="form-control" placeholder="VD: Trung tâm bảo hành Dell, FPT Services, IT Nội bộ...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mô tả sự cố hoặc linh kiện cần nâng cấp <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Chi tiết: Máy bật không lên nguồn, sọc màn hình, hoặc nâng cấp RAM từ 8GB lên 16GB..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= url('maintenance') ?>" class="btn btn-secondary px-4">Hủy</a>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">
                            <i class="bi bi-tools me-1"></i> Tạo phiếu bảo trì
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
