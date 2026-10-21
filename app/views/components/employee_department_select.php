<div class="employee-department-select dropdown position-relative" data-employee-search-select data-department-select-id="<?= e($employeeDepartmentSelectId) ?>">
    <button
        type="button"
        class="<?= e($employeeSelectClass) ?> text-start"
        data-employee-select-toggle
        aria-label="<?= e($employeeSelectPlaceholder) ?>"
        aria-haspopup="listbox"
        aria-expanded="false">
        <?= e($employeeSelectPlaceholder) ?>
    </button>
    <select
        name="<?= e($employeeSelectName) ?>"
        id="<?= e($employeeSelectId) ?>"
        class="d-none"
        data-employee-select
        data-required="<?= !empty($employeeSelectRequired) ? '1' : '0' ?>"
        tabindex="-1"
        aria-hidden="true">
        <option value=""><?= e($employeeSelectPlaceholder) ?></option>
        <?php foreach ($employeeSelectOptions as $employeeOption): ?>
            <option
                value="<?= (int)$employeeOption['id'] ?>"
                data-department-id="<?= (int)$employeeOption['department_id'] ?>">
                <?= e($employeeOption['fullname']) ?> (<?= e($employeeOption['code']) ?> - <?= e($employeeOption['department_name']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <div class="dropdown-menu w-100 p-2" data-employee-select-menu>
        <input
            type="search"
            class="form-control form-control-sm mb-2"
            data-employee-search-input
            aria-label="Tìm nhân viên theo họ tên hoặc mã nhân viên"
            placeholder="Tìm theo tên hoặc mã nhân viên..."
            autocomplete="off">
        <div class="employee-select-options" data-employee-select-options role="listbox"></div>
        <div class="small text-muted px-2 py-1 d-none" data-employee-no-results>Không tìm thấy nhân viên phù hợp.</div>
    </div>
</div>
