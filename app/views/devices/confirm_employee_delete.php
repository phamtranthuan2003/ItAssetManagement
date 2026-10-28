<div class="card border-warning shadow-sm">
    <div class="card-header bg-warning-subtle fw-bold text-dark">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận xóa nhân viên và trả thiết bị về kho
    </div>
    <div class="card-body">
        <p>
            Nhân viên <strong><?= e($employee['fullname']) ?></strong> (<?= e($employee['code']) ?>)
            đang sử dụng <?= count($otherDevices) ?> thiết bị khác ngoài thiết bị STT
            <strong><?= (int)$device['id'] ?></strong> bạn đang xóa.
        </p>
        <p>Tiếp tục sẽ xóa nhân viên này và đưa các thiết bị sau về kho:</p>
        <ul class="list-group mb-4">
            <?php foreach ($otherDevices as $otherDevice): ?>
                <li class="list-group-item">
                    <strong>STT <?= (int)$otherDevice['id'] ?></strong>
                    — <?= e(trim(($otherDevice['brand'] ?? '') . ' ' . ($otherDevice['model'] ?? '')) ?: $otherDevice['type']) ?>
                    <span class="text-muted">(<?= e($otherDevice['device_code']) ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="alert alert-danger" role="alert">
            Thiết bị STT <?= (int)$device['id'] ?> và lịch sử/chi tiết phiếu liên quan sẽ bị xóa.
            Thao tác này không thể hoàn tác.
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="<?= url($returnTo) ?>" class="btn btn-outline-secondary">Hủy</a>
            <form action="<?= url('devices/' . (int)$device['id'] . '/delete') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
                <input type="hidden" name="delete_employee" value="1">
                <input type="hidden" name="confirm_other_devices" value="1">
                <button type="submit" class="btn btn-danger">
                    Xác nhận xóa nhân viên và trả thiết bị về kho
                </button>
            </form>
        </div>
    </div>
</div>
