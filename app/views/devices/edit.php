<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa thiết bị STT <strong><?= (int)$device['id'] ?></strong></span>
                <a href="<?= url('devices/' . $device['id'] . '?' . http_build_query(['return_to' => $returnTo])) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Trở về chi tiết</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('devices/' . $device['id'] . '/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">

                    <!-- 1. THÔNG TIN CHUNG -->
                    <h6 class="text-primary fw-bold text-uppercase mb-3 pb-2 border-bottom">
                        <i class="bi bi-info-circle me-1"></i> 1. Thông tin chung
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">STT</label>
                            <input type="text" class="form-control" value="<?= (int)$device['id'] ?>" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Loại thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control" value="<?= e($device['type']) ?>" list="deviceTypes" required>
                            <datalist id="deviceTypes">
                                <option value="PC">
                                <option value="Laptop">
                                <option value="Máy CNC">
                                <option value="Máy in">
                            </datalist>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hãng sản xuất</label>
                            <input type="text" name="brand" class="form-control" value="<?= e($device['brand']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên / Model</label>
                            <input type="text" name="model" class="form-control" value="<?= e($device['model']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" value="<?= e($device['serial_number'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ngày nhập kho</label>
                            <input type="date" name="import_date" class="form-control" value="<?= e($device['import_date']) ?>">
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
                        <i class="bi bi-cpu me-1"></i> 2. Thông số kỹ thuật (không bắt buộc)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bộ vi xử lý (CPU)</label>
                            <input type="text" name="cpu" class="form-control" value="<?= e($device['cpu'] ?? '') ?>">
                        </div>

                        <div class="col-md-6" id="fieldMainboard">
                            <label class="form-label fw-semibold">Mainboard (Bo mạch chủ)</label>
                            <input type="text" name="mainboard" class="form-control" value="<?= e($device['mainboard'] ?? '') ?>">
                        </div>

                        <div class="col-md-6" id="fieldScreenSize">
                            <label class="form-label fw-semibold">Màn hình tích hợp (nếu có)</label>
                            <input type="text" name="screen_size" class="form-control" value="<?= e($device['screen_size'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="has_monitors" id="hasMonitors" class="form-check-input" value="1" <?= !empty($device['monitors']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="hasMonitors">Có kèm màn hình rời</label>
                            </div>
                        </div>
                        <div class="col-12 <?= !empty($device['monitors']) ? '' : 'd-none' ?>" id="monitorFields">
                            <div id="monitorList" class="d-flex flex-column gap-3">
                                <?php foreach (($device['monitors'] ?? []) as $index => $monitor): ?>
                                <div class="monitor-row border rounded p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Hãng màn hình <span class="text-danger">*</span></label>
                                            <input type="text" name="monitors[<?= $index ?>][brand]" data-field="brand" class="form-control" value="<?= e($monitor['brand']) ?>" placeholder="VD: Dell, LG, Samsung">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Model màn hình</label>
                                            <input type="text" name="monitors[<?= $index ?>][model]" data-field="model" class="form-control" value="<?= e($monitor['model'] ?? '') ?>" placeholder="Không bắt buộc">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Kích thước (inch) <span class="text-danger">*</span></label>
                                            <input type="number" name="monitors[<?= $index ?>][size]" data-field="size" class="form-control" value="<?= e($monitor['size']) ?>" min="1" max="100" step="0.1" placeholder="VD: 24">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-monitor" aria-label="Xóa màn hình">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addMonitor">
                                <i class="bi bi-plus-lg me-1"></i> Thêm màn hình
                            </button>
                            <template id="monitorTemplate">
                                <div class="monitor-row border rounded p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Hãng màn hình <span class="text-danger">*</span></label>
                                            <input type="text" data-field="brand" class="form-control" placeholder="VD: Dell, LG, Samsung">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Model màn hình</label>
                                            <input type="text" data-field="model" class="form-control" placeholder="Không bắt buộc">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Kích thước (inch) <span class="text-danger">*</span></label>
                                            <input type="number" data-field="size" class="form-control" min="1" max="100" step="0.1" placeholder="VD: 24">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-monitor" aria-label="Xóa màn hình">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dung lượng RAM (GB)</label>
                            <input type="number" name="ram_gb" class="form-control" value="<?= e($device['ram_gb'] ?? '') ?>" min="1" max="512">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Loại RAM & Bus</label>
                            <input type="text" name="ram_type" class="form-control" value="<?= e($device['ram_type'] ?? '') ?>">
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

    <script>
        const hasMonitors = document.getElementById('hasMonitors');
        const monitorFields = document.getElementById('monitorFields');
        const monitorList = document.getElementById('monitorList');
        const monitorTemplate = document.getElementById('monitorTemplate');

        function addMonitor(values = {}) {
            const row = monitorTemplate.content.firstElementChild.cloneNode(true);
            row.querySelectorAll('[data-field]').forEach((input) => {
                const field = input.dataset.field;
                input.name = `monitors[${monitorList.children.length}][${field}]`;
                input.value = values[field] || '';
            });
            row.querySelector('.remove-monitor').addEventListener('click', () => {
                row.remove();
                if (monitorList.children.length === 0) {
                    hasMonitors.checked = false;
                    toggleMonitorFields();
                } else {
                    renumberMonitors();
                }
            });
            monitorList.appendChild(row);
            toggleMonitorFields();
        }

        function renumberMonitors() {
            monitorList.querySelectorAll('.monitor-row').forEach((row, index) => {
                row.querySelectorAll('[data-field]').forEach((input) => {
                    input.name = `monitors[${index}][${input.dataset.field}]`;
                });
            });
        }

        function toggleMonitorFields() {
            const enabled = hasMonitors.checked;
            monitorFields.classList.toggle('d-none', !enabled);
            monitorList.querySelectorAll('[data-field]').forEach((input) => {
                input.disabled = !enabled;
                input.required = enabled && (input.dataset.field === 'brand' || input.dataset.field === 'size');
            });
            if (enabled && monitorList.children.length === 0) {
                addMonitor();
            }
        }

        monitorList.querySelectorAll('.remove-monitor').forEach((button) => {
            button.addEventListener('click', () => {
                button.closest('.monitor-row').remove();
                if (monitorList.children.length === 0) {
                    hasMonitors.checked = false;
                    toggleMonitorFields();
                } else {
                    renumberMonitors();
                }
            });
        });
        hasMonitors.addEventListener('change', toggleMonitorFields);
        document.getElementById('addMonitor').addEventListener('click', () => addMonitor());
        toggleMonitorFields();
    </script>
</div>
