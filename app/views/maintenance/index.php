<div class="card mb-4">
    <div class="card-body">
        <form action="<?= url('maintenance') ?>" method="GET" class="row g-3 align-items-end" data-auto-filter>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Thiết bị</label>
                <select name="device_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả thiết bị --</option>
                    <?php foreach ($devices as $device): ?>
                        <option value="<?= (int)$device['id'] ?>" <?= (string)$filters['device_id'] === (string)$device['id'] ? 'selected' : '' ?>>
                            STT <?= (int)$device['id'] ?> - <?= e(trim(($device['type'] ?? '') . ' ' . ($device['brand'] ?? '') . ' ' . ($device['model'] ?? ''))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Trạng thái bảo dưỡng</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>Đang xử lý / sửa chữa</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                    <option value="cannot_repair" <?= $status === 'cannot_repair' ? 'selected' : '' ?>>Không thể sửa chữa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Người phụ trách</label>
                <select name="created_by" class="form-select form-select-sm">
                    <option value="">-- Tất cả người phụ trách --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int)$user['id'] ?>" <?= (string)$filters['created_by'] === (string)$user['id'] ? 'selected' : '' ?>>
                            <?= e($user['fullname']) ?> (<?= e($user['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Thời gian bắt đầu</label>
                <div class="input-group input-group-sm">
                    <input type="date" name="date_from" class="form-control" aria-label="Từ ngày" value="<?= e($filters['date_from']) ?>">
                    <input type="date" name="date_to" class="form-control" aria-label="Đến ngày" value="<?= e($filters['date_to']) ?>">
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="<?= url('maintenance') ?>" class="btn btn-light btn-sm border">Đặt lại</a>
                <a href="<?= url('maintenance/create') ?>" class="btn btn-warning text-dark btn-sm fw-semibold float-end">
                    <i class="bi bi-tools me-1"></i> Tạo phiếu bảo trì / Nâng cấp mới
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Thiết bị</th>
                    <th>Loại bảo dưỡng</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày xong</th>
                    <th>Chi phí</th>
                    <th>Đơn vị thực hiện</th>
                    <th>Người phụ trách</th>
                    <th>Nội dung sự cố / Nâng cấp</th>
                    <th>Trạng thái</th>
                    <th class="text-end" style="width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted"><?= !empty($filters['device_id']) || !empty($filters['created_by']) || !empty($filters['date_from']) || !empty($filters['date_to']) || !empty($status) ? 'Không có kết quả phù hợp với bộ lọc.' : 'Chưa có thông tin bảo dưỡng nào.' ?></td>
                </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                    <tr>
                        <td>
                            <a href="<?= url('devices/' . $r['device_id']) ?>" class="fw-bold text-decoration-none">
                                STT <?= (int)$r['device_id'] ?>
                            </a>
                            <div class="small text-muted"><?= e($r['model']) ?> (<?= e($r['type']) ?>)</div>
                        </td>
                        <td>
                            <span class="badge <?= $r['maintenance_type'] === 'upgrade' ? 'bg-info' : ($r['maintenance_type'] === 'repair' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                <?= match ($r['maintenance_type']) {
                                    'upgrade' => 'Nâng cấp linh kiện',
                                    'repair'  => 'Sửa chữa',
                                    default   => 'Bảo dưỡng định kỳ'
                                } ?>
                            </span>
                        </td>
                        <td><?= formatDate($r['start_date']) ?></td>
                        <td><?= formatDate($r['end_date']) ?></td>
                        <td><strong class="text-dark"><?= formatMoney($r['cost']) ?></strong></td>
                        <td><?= e($r['provider'] ?? 'Nội bộ') ?></td>
                        <td><?= e($r['creator_name']) ?></td>
                        <td>
                            <div class="small text-secondary" style="max-width: 250px;"><?= e($r['description']) ?></div>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'completed'): ?>
                                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Đã hoàn thành</span>
                            <?php elseif ($r['status'] === 'in_progress'): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Đang xử lý</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Không thể sửa</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($r['status'] === 'in_progress'): ?>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?= $r['id'] ?>" title="Cập nhật hoàn tất">
                                <i class="bi bi-check2-circle me-1"></i> Xong
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm disabled">
                                Đã xong
                            </button>
                            <?php endif; ?>

                            <!-- Modal Cập nhật hoàn thành -->
                            <?php if ($r['status'] === 'in_progress'): ?>
                            <div class="modal fade text-start" id="updateStatusModal<?= $r['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?= url('maintenance/' . $r['id'] . '/status') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="bi bi-tools me-2 text-primary"></i>Hoàn tất bảo trì thiết bị STT <?= (int)$r['device_id'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Kết quả xử lý <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="completed" selected>Hoàn thành tốt (Đưa thiết bị về Trong kho)</option>
                                                        <option value="cannot_repair">Không thể sửa chữa (Chuyển sang diện Thanh lý)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Ngày hoàn tất <span class="text-danger">*</span></label>
                                                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Chi phí thực tế (VNĐ)</label>
                                                    <input type="number" name="cost" class="form-control" value="<?= (int)$r['cost'] ?>" min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Ghi chú kết quả sửa chữa</label>
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="Chi tiết linh kiện đã thay, tình trạng sau khi test..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-success">Cập nhật và hoàn tất</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
