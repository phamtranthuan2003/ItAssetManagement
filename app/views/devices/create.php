<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-plus-circle me-2 text-primary"></i>Nhập mới thiết bị vào kho</span>
                <a href="<?= url('devices') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Trở về danh sách</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('devices/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- 1. THÔNG TIN CHUNG -->
                    <h6 class="text-primary fw-bold text-uppercase mb-3 pb-2 border-bottom">
                        <i class="bi bi-info-circle me-1"></i> 1. Thông tin chung
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="device_code" class="form-control" placeholder="VD: PC-DELL-004 hoặc LT-HP-005" required autofocus>
                            <div class="form-text small">Mã định danh duy nhất trong toàn hệ thống.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Loại thiết bị <span class="text-danger">*</span></label>
                            <select name="type" id="deviceTypeSelect" class="form-select" required>
                                <option value="Laptop">Laptop (Máy tính xách tay)</option>
                                <option value="PC">PC (Máy tính để bàn / Desktop)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hãng sản xuất <span class="text-danger">*</span></label>
                            <input type="text" name="brand" class="form-control" placeholder="Dell, HP, Lenovo, Asus, Apple..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên Model <span class="text-danger">*</span></label>
                            <input type="text" name="model" class="form-control" placeholder="VD: Latitude 5420, ThinkPad T14, Optiplex 7080..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="Số Serial in trên thân máy hoặc đáy máy">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày nhập kho <span class="text-danger">*</span></label>
                            <input type="date" name="import_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày hết hạn bảo hành</label>
                            <input type="date" name="warranty_expiry" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tình trạng nhập <span class="text-danger">*</span></label>
                            <select name="condition_state" class="form-select">
                                <option value="new" selected>Mới 100% nguyên seal</option>
                                <option value="used">Đã qua sử dụng (Tốt)</option>
                                <option value="broken">Hỏng hóc / Cần kiểm tra</option>
                                <option value="under_warranty">Đang trong thời hạn bảo hành</option>
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
                            <input type="text" name="cpu" class="form-control" placeholder="VD: Intel Core i5-12400 hoặc AMD Ryzen 5 5600G" required>
                        </div>

                        <!-- Mainboard chỉ hiển thị khi chọn PC -->
                        <div class="col-md-6" id="fieldMainboard" style="display: none;">
                            <label class="form-label fw-semibold">Mainboard (Bo mạch chủ - Chỉ PC)</label>
                            <input type="text" name="mainboard" class="form-control" placeholder="VD: ASUS B660M, MSI B550M, Dell Proprietary...">
                        </div>

                        <!-- Màn hình chỉ hiển thị khi chọn Laptop -->
                        <div class="col-md-6" id="fieldScreenSize">
                            <label class="form-label fw-semibold">Màn hình (Kích thước & Độ phân giải - Laptop)</label>
                            <input type="text" name="screen_size" class="form-control" placeholder="VD: 14 inch Full HD IPS 300 nits">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dung lượng RAM (GB) <span class="text-danger">*</span></label>
                            <input type="number" name="ram_gb" class="form-control" value="16" min="2" max="512" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Loại RAM & Bus</label>
                            <input type="text" name="ram_type" class="form-control" value="DDR4 3200MHz" placeholder="VD: DDR4 3200MHz, DDR5 5600MHz">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ổ cứng SSD</label>
                            <input type="text" name="ssd_capacity" class="form-control" value="512GB NVMe M.2" placeholder="VD: 256GB NVMe, 512GB PCIe Gen 4">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ổ cứng HDD phụ (nếu có)</label>
                            <input type="text" name="hdd_capacity" class="form-control" placeholder="VD: 1TB 7200RPM hoặc để trống">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Card đồ họa (GPU)</label>
                            <input type="text" name="gpu" class="form-control" placeholder="VD: NVIDIA RTX 3060 12GB hoặc Intel Iris Xe Onboard">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú cấu hình khác</label>
                            <input type="text" name="spec_notes" class="form-control" placeholder="Bàn phím LED, bảo mật vân tay, nguồn máy, vỏ case...">
                        </div>
                    </div>

                    <!-- 3. GHI CHÚ NHẬP KHO -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú nhập kho bổ sung</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú về nguồn gốc nhập, nhà cung cấp, phụ kiện ban đầu..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= url('devices') ?>" class="btn btn-secondary px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Lưu thiết bị vào kho
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
