<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-file-earmark-plus me-2 text-primary"></i>
                    Lập phiếu: 
                    <strong class="text-primary text-uppercase">
                        <?= match ($type) {
                            'allocation' => 'Cấp phát thiết bị',
                            'transfer'   => 'Chuyển giao điều chuyển',
                            'revocation' => 'Thu hồi thiết bị về kho',
                            default      => 'Cấp phát thiết bị'
                        } ?>
                    </strong>
                </span>
                <div class="btn-group btn-group-sm">
                    <a href="<?= url('tickets/create?type=allocation') ?>" class="btn <?= $type === 'allocation' ? 'btn-primary' : 'btn-outline-primary' ?>">Cấp phát</a>
                    <a href="<?= url('tickets/create?type=transfer') ?>" class="btn <?= $type === 'transfer' ? 'btn-info text-white' : 'btn-outline-info' ?>">Chuyển giao</a>
                    <a href="<?= url('tickets/create?type=revocation') ?>" class="btn <?= $type === 'revocation' ? 'btn-danger' : 'btn-outline-danger' ?>">Thu hồi</a>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('tickets/store') ?>" method="POST" id="ticketForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="ticket_type" value="<?= e($type) ?>">

                    <!-- 1. THÔNG TIN CHUNG CỦA PHIẾU -->
                    <h6 class="text-primary fw-bold text-uppercase mb-3 pb-2 border-bottom">
                        <i class="bi bi-card-checklist me-1"></i> 1. Thông tin phiếu
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ngày thực hiện <span class="text-danger">*</span></label>
                            <input type="date" name="ticket_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- CẤP PHÁT: KHO -> NHÂN VIÊN & PHÒNG BAN -->
                        <?php if ($type === 'allocation'): ?>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nhân viên nhận cấp phát <span class="text-danger">*</span></label>
                                <select name="to_employee_id" id="toEmployeeSelect" class="form-select" required>
                                    <option value="">-- Chọn nhân viên nhận --</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" data-department-id="<?= $emp['department_id'] ?>">
                                            <?= e($emp['fullname']) ?> (<?= e($emp['code']) ?> - <?= e($emp['department_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Phòng ban tiếp nhận <span class="text-danger">*</span></label>
                                <select name="to_department_id" id="toDepartmentSelect" class="form-select" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?> (<?= e($dept['code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Cấp phát tạm thời -->
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_temporary" value="1" id="isTemporaryCheck">
                                        <label class="form-check-label fw-semibold text-primary" for="isTemporaryCheck">
                                            <i class="bi bi-clock-history me-1"></i> Đây là cấp phát mượn tạm thời (có thời hạn trả)
                                        </label>
                                    </div>
                                    <div class="mt-2 col-md-4" id="expectedReturnDateField" style="display: none;">
                                        <label class="form-label small fw-semibold text-danger">Ngày dự kiến hoàn trả <span class="text-danger">*</span></label>
                                        <input type="date" name="expected_return_date" class="form-control form-control-sm">
                                        <div class="form-text small">Hệ thống sẽ hiển thị cảnh báo đỏ trên Dashboard khi quá thời hạn này.</div>
                                    </div>
                                </div>
                            </div>

                        <!-- CHUYỂN GIAO: TỪ NGƯỜI NÀY SANG NGƯỜI KHÁC -->
                        <?php elseif ($type === 'transfer'): ?>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-box-arrow-right me-1"></i>Bên bàn giao (Hiện tại)</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Nhân viên bàn giao</label>
                                        <select name="from_employee_id" class="form-select form-select-sm">
                                            <option value="">-- Chọn nhân viên đang giữ --</option>
                                            <?php foreach ($employees as $emp): ?>
                                                <option value="<?= $emp['id'] ?>"><?= e($emp['fullname']) ?> (<?= e($emp['code']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label small fw-semibold">Phòng ban giao</label>
                                        <select name="from_department_id" class="form-select form-select-sm">
                                            <option value="">-- Chọn phòng ban --</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-info-subtle rounded border border-info-subtle h-100">
                                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-box-arrow-in-right me-1"></i>Bên tiếp nhận mới</h6>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Nhân viên tiếp nhận mới <span class="text-danger">*</span></label>
                                        <select name="to_employee_id" id="toEmployeeSelect" class="form-select form-select-sm" required>
                                            <option value="">-- Chọn nhân viên mới --</option>
                                            <?php foreach ($employees as $emp): ?>
                                                <option value="<?= $emp['id'] ?>" data-department-id="<?= $emp['department_id'] ?>">
                                                    <?= e($emp['fullname']) ?> (<?= e($emp['code']) ?> - <?= e($emp['department_name']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label small fw-semibold">Phòng ban mới <span class="text-danger">*</span></label>
                                        <select name="to_department_id" id="toDepartmentSelect" class="form-select form-select-sm" required>
                                            <option value="">-- Chọn phòng ban --</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        <!-- THU HỒI: TỪ NHÂN VIÊN VỀ LẠI KHO -->
                        <?php elseif ($type === 'revocation'): ?>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nhân viên bàn giao lại thiết bị <span class="text-danger">*</span></label>
                                <select name="from_employee_id" id="toEmployeeSelect" class="form-select" required>
                                    <option value="">-- Chọn nhân viên hoàn trả --</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" data-department-id="<?= $emp['department_id'] ?>">
                                            <?= e($emp['fullname']) ?> (<?= e($emp['code']) ?> - <?= e($emp['department_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phòng ban của nhân viên <span class="text-danger">*</span></label>
                                <select name="from_department_id" id="toDepartmentSelect" class="form-select" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Lý do thực hiện</label>
                            <input type="text" name="reason" class="form-control" placeholder="VD: Cấp máy làm việc cho nhân sự mới / Điều chuyển phòng ban / Bàn giao khi nghỉ việc...">
                        </div>
                    </div>

                    <!-- 2. DANH SÁCH THIẾT BỊ VÀ PHỤ KIỆN ĐI KÈM (ĐA THIẾT BỊ) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <h6 class="text-primary fw-bold text-uppercase mb-0">
                            <i class="bi bi-devices me-1"></i> 2. Danh sách thiết bị & phụ kiện đi kèm
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddDeviceRow">
                            <i class="bi bi-plus-lg me-1"></i> Thêm thiết bị vào phiếu
                        </button>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="devicesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 32%;">Thiết bị <span class="text-danger">*</span></th>
                                    <th>Phụ kiện kèm theo (Chuột, phím, sạc, màn hình...)</th>
                                    <?php if ($type === 'revocation'): ?>
                                    <th style="width: 25%;">Tình trạng khi thu hồi</th>
                                    <?php endif; ?>
                                    <th style="width: 50px;" class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="deviceRowsContainer">
                                <!-- Hàng mẫu 1 -->
                                <tr>
                                    <td>
                                        <select name="device_id[]" class="form-select form-select-sm" required>
                                            <option value="">-- Chọn thiết bị --</option>
                                            <?php if ($type === 'allocation'): ?>
                                                <?php foreach ($inStockDevices as $dev): ?>
                                                    <option value="<?= $dev['id'] ?>">
                                                        [<?= e($dev['device_code']) ?>] <?= e($dev['type']) ?> <?= e($dev['brand']) ?> <?= e($dev['model']) ?> (RAM <?= $dev['ram_gb'] ?>GB)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <?php foreach ($allocatedDevices as $dev): ?>
                                                    <option value="<?= $dev['id'] ?>">
                                                        [<?= e($dev['device_code']) ?>] <?= e($dev['type']) ?> <?= e($dev['brand']) ?> <?= e($dev['model']) ?> - Đang cấp: <?= e($dev['employee_name'] ?? 'Chưa rõ') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="accessories[]" class="form-control form-control-sm" placeholder="VD: Chuột có dây, Củ sạc Type-C 65W, Màn hình 24 inch...">
                                    </td>
                                    <?php if ($type === 'revocation'): ?>
                                    <td>
                                        <select name="returned_condition[]" class="form-select form-select-sm" required>
                                            <option value="good" selected>Tốt -> Tự về 'Trong kho'</option>
                                            <option value="broken">Hỏng hóc -> Chuyển 'Bảo trì'</option>
                                            <option value="missing_accessories">Thiếu phụ kiện</option>
                                        </select>
                                    </td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-row" title="Xóa dòng này">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 3. GHI CHÚ PHIẾU -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú phiếu biên bản</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú thêm về điều kiện sử dụng, thỏa thuận bảo quản tài sản..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= url('tickets') ?>" class="btn btn-secondary px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Hoàn tất và Tạo phiếu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAddRow = document.getElementById('btnAddDeviceRow');
    const container = document.getElementById('deviceRowsContainer');

    if (btnAddRow && container) {
        btnAddRow.addEventListener('click', function () {
            const firstRow = container.querySelector('tr');
            if (firstRow) {
                const clone = firstRow.cloneNode(true);
                // Reset giá trị
                const select = clone.querySelector('select');
                if (select) select.selectedIndex = 0;
                const input = clone.querySelector('input[type="text"]');
                if (input) input.value = '';
                container.appendChild(clone);
            }
        });

        container.addEventListener('click', function (e) {
            if (e.target.closest('.btn-delete-row')) {
                const rows = container.querySelectorAll('tr');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Phiếu phải có ít nhất một thiết bị!');
                }
            }
        });
    }
});
</script>
