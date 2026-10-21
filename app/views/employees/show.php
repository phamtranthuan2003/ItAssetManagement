<div class="row g-4">
    <!-- CỘT TRÁI: THÔNG TIN NHÂN VIÊN & CHỈNH SỬA -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-vcard me-2 text-primary"></i>Hồ sơ nhân viên</span>
                <span class="badge <?= $employee['status'] === 'working' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $employee['status'] === 'working' ? 'Đang làm việc' : 'Đã nghỉ việc' ?>
                </span>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('employees/' . $employee['id'] . '/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">STT</label>
                        <input type="text" class="form-control" value="<?= (int)$employee['id'] ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" value="<?= e($employee['fullname']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phòng ban</label>
                        <select name="department_id" class="form-select" required>
                            <?php 
                            $deptModel = new Department();
                            $allDepts = $deptModel->getAll();
                            foreach ($allDepts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $employee['department_id'] == $d['id'] ? 'selected' : '' ?>>
                                    <?= e($d['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Chức vụ</label>
                        <input type="text" name="position" class="form-control" value="<?= e($employee['position'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($employee['phone'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($employee['email'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Tình trạng nhân sự</label>
                        <select name="status" class="form-select">
                            <option value="working" <?= $employee['status'] === 'working' ? 'selected' : '' ?>>Đang làm việc</option>
                            <option value="resigned" <?= $employee['status'] === 'resigned' ? 'selected' : '' ?>>Đã nghỉ việc</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Lưu thông tin nhân viên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: DANH SÁCH THIẾT BỊ ĐANG GIỮ & TÍNH NĂNG THU HỒI KHI NGHỈ VIỆC -->
    <div class="col-lg-8">
        <!-- KHỐI CẢNH BÁO VÀ THU HỒI HÀNG LOẠT -->
        <?php if (!empty($heldDevices)): ?>
            <?php if ($employee['status'] === 'resigned'): ?>
                <div class="alert alert-danger shadow-sm border-danger d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Nhân viên đã nghỉ việc nhưng vẫn đang giữ thiết bị!</h6>
                        <p class="mb-0 small">Cần thu hồi toàn bộ tài sản công ty ngay trước khi hoàn tất thủ tục thanh lý hợp đồng.</p>
                    </div>
                    <button type="button" class="btn btn-danger text-nowrap" data-bs-toggle="modal" data-bs-target="#bulkRevokeModal">
                        <i class="bi bi-box-arrow-in-left me-1"></i> Thu hồi tất cả (<?= count($heldDevices) ?> máy)
                    </button>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-laptop me-2 text-primary"></i>Thiết bị đang nắm giữ (<?= count($heldDevices) ?>)
                    </h5>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkRevokeModal">
                        <i class="bi bi-box-arrow-in-left me-1"></i> Thu hồi tất cả về kho
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-laptop me-2 text-primary"></i>Thiết bị đang nắm giữ
                </h5>
            </div>
        <?php endif; ?>

        <!-- BẢNG THIẾT BỊ ĐANG GIỮ -->
        <div class="card mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Loại & Tên model</th>
                            <th>Cấu hình</th>
                            <th>Hình thức</th>
                            <th>Thời hạn</th>
                            <th class="text-end">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($heldDevices)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-check2-circle fs-3 text-success d-block mb-1"></i>
                                Nhân viên hiện không nắm giữ thiết bị nào.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($heldDevices as $hd): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('devices/' . $hd['id']) ?>" class="fw-bold text-decoration-none">
                                        <?= (int)$hd['id'] ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border me-1"><?= e($hd['type']) ?></span>
                                    <strong><?= e(trim($hd['brand'] . ' ' . $hd['model']) ?: '—') ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted d-block"><?= e($hd['cpu']) ?></small>
                                    <small class="text-muted">RAM <?= e($hd['ram_gb']) ?>GB | SSD <?= e($hd['ssd_capacity'] ?? '—') ?></small>
                                </td>
                                <td>
                                    <?php if ($hd['is_temporary']): ?>
                                        <span class="badge bg-warning text-dark">Mượn tạm</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Dài hạn</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($hd['is_temporary']): ?>
                                        <small class="text-danger fw-bold"><?= formatDate($hd['expected_return_date']) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">—</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url('devices/' . $hd['id']) ?>" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= url('employees') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Trở về danh sách nhân viên
            </a>
            <?php if ($employee['status'] === 'working'): ?>
            <a href="<?= url('tickets/create?type=allocation') ?>" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> Lập phiếu cấp phát mới cho nhân viên này
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL THU HỒI HÀNG LOẠT THIẾT BỊ -->
<?php if (!empty($heldDevices)): ?>
<div class="modal fade" id="bulkRevokeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= url('employees/' . $employee['id'] . '/bulk-revoke') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-left me-2"></i>Thu hồi tất cả thiết bị</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Hệ thống sẽ tự động tạo một phiếu thu hồi và chuyển trạng thái của <strong><?= count($heldDevices) ?> thiết bị</strong> sau về kho:</p>
                    <ul class="list-group mb-3">
                        <?php foreach ($heldDevices as $hd): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center small py-2">
                            <span><strong>STT <?= (int)$hd['id'] ?></strong> - <?= e(trim($hd['brand'] . ' ' . $hd['model']) ?: '—') ?></span>
                            <span class="badge bg-secondary"><?= e($hd['type']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tình trạng thiết bị khi thu nhận lại <span class="text-danger">*</span></label>
                        <select name="returned_condition" class="form-select" required>
                            <option value="good" selected>Tốt (Hoạt động bình thường -> Về 'Trong kho')</option>
                            <option value="broken">Hỏng hóc / Lỗi (-> Chuyển sang 'Bảo trì')</option>
                            <option value="missing_accessories">Thiếu phụ kiện (-> Về kho kèm ghi chú)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do thu hồi</label>
                        <input type="text" name="reason" class="form-control" value="Thu hồi thiết bị khi nhân viên bàn giao/nghỉ việc">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú kiểm kê phụ kiện</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ghi nhận củ sạc, chuột, bàn phím, tình trạng trầy xước..."></textarea>
                    </div>

                    <?php if ($employee['status'] === 'working'): ?>
                    <div class="form-check p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="mark_as_resigned" value="1" id="markResigned">
                        <label class="form-check-label fw-semibold text-danger" for="markResigned">
                            Đồng thời cập nhật tình trạng nhân viên thành: "Đã nghỉ việc"
                        </label>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận thu hồi ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
