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
                            <label class="form-label fw-semibold">STT</label>
                            <input type="text" class="form-control" value="Tự động khi lưu" readonly>
                            <div class="form-text small">Thiết bị mới sẽ có STT lớn hơn và đứng đầu danh sách.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Loại thiết bị <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control" list="deviceTypes" placeholder="VD: PC, Laptop, Máy CNC, Máy in..." required autofocus>
                            <datalist id="deviceTypes">
                                <option value="PC">
                                <option value="Laptop">
                                <option value="Máy CNC">
                                <option value="Máy in">
                            </datalist>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hãng sản xuất</label>
                            <input type="text" name="brand" class="form-control" placeholder="Không bắt buộc">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên / Model</label>
                            <input type="text" name="model" class="form-control" placeholder="Không bắt buộc">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="Số Serial in trên thân máy hoặc đáy máy">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày nhập kho</label>
                            <input type="date" name="import_date" class="form-control" value="<?= date('Y-m-d') ?>">
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
                        <i class="bi bi-cpu me-1"></i> 2. Thông số kỹ thuật (không bắt buộc)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bộ vi xử lý (CPU)</label>
                            <input type="text" name="cpu" class="form-control" placeholder="Không bắt buộc">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mainboard (Bo mạch chủ)</label>
                            <input type="text" name="mainboard" class="form-control" placeholder="VD: ASUS B660M, MSI B550M, Dell Proprietary...">
                        </div>

                        <div class="col-md-6" id="fieldScreenSize">
                            <label class="form-label fw-semibold">Màn hình tích hợp (nếu có)</label>
                            <input type="text" name="screen_size" class="form-control" placeholder="VD: 14 inch Full HD IPS 300 nits">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="has_monitors" id="hasMonitors" class="form-check-input" value="1">
                                <label class="form-check-label fw-semibold" for="hasMonitors">Có kèm màn hình rời</label>
                            </div>
                        </div>
                        <div class="col-12 d-none" id="monitorFields">
                            <div id="monitorList" class="d-flex flex-column gap-3"></div>
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
                            <input type="number" name="ram_gb" class="form-control" min="1" max="512">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Loại RAM & Bus</label>
                            <input type="text" name="ram_type" class="form-control" placeholder="VD: DDR4 3200MHz, DDR5 5600MHz">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ổ cứng SSD</label>
                            <input type="text" name="ssd_capacity" class="form-control" placeholder="VD: 256GB NVMe, 512GB PCIe Gen 4">
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

    <script>
        const hasMonitors = document.getElementById('hasMonitors');
        const monitorFields = document.getElementById('monitorFields');
        const monitorList = document.getElementById('monitorList');
        const monitorTemplate = document.getElementById('monitorTemplate');

        function addMonitor(values = {}) {
            const row = monitorTemplate.content.firstElementChild.cloneNode(true);
            const index = monitorList.children.length;
            row.querySelectorAll('[data-field]').forEach((input) => {
                const field = input.dataset.field;
                input.name = `monitors[${index}][${field}]`;
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

        hasMonitors.addEventListener('change', toggleMonitorFields);
        document.getElementById('addMonitor').addEventListener('click', () => addMonitor());
        toggleMonitorFields();
    </script>
</div>
