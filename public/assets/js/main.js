/**
 * File: public/assets/js/main.js
 * JavaScript hỗ trợ giao diện và logic client-side
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Tự động đóng alert sau 5 giây
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 6000);
    });

    // 2. Chuyển đổi hiển thị trường cấu hình theo Loại thiết bị (PC vs Laptop)
    const deviceTypeSelect = document.getElementById('deviceTypeSelect');
    if (deviceTypeSelect) {
        function toggleDeviceFields() {
            const val = deviceTypeSelect.value;
            const mainboardField = document.getElementById('fieldMainboard');
            const screenField = document.getElementById('fieldScreenSize');

            if (mainboardField) {
                mainboardField.style.display = (val === 'PC') ? 'block' : 'none';
            }
            if (screenField) {
                screenField.style.display = (val === 'Laptop') ? 'block' : 'none';
            }
        }
        deviceTypeSelect.addEventListener('change', toggleDeviceFields);
        toggleDeviceFields();
    }

    // 3. Tự động điền phòng ban khi chọn nhân viên trong form cấp phát
    const toEmployeeSelect = document.getElementById('toEmployeeSelect');
    if (toEmployeeSelect) {
        toEmployeeSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const deptId = selectedOption.getAttribute('data-department-id');
            const toDeptSelect = document.getElementById('toDepartmentSelect');
            if (toDeptSelect && deptId) {
                toDeptSelect.value = deptId;
            }
        });
    }

    // 4. Bật/tắt chọn hạn trả khi check Cấp phát tạm thời
    const isTemporaryCheck = document.getElementById('isTemporaryCheck');
    const returnDateField = document.getElementById('expectedReturnDateField');
    if (isTemporaryCheck && returnDateField) {
        function toggleReturnDate() {
            returnDateField.style.display = isTemporaryCheck.checked ? 'block' : 'none';
            const input = returnDateField.querySelector('input');
            if (input) {
                input.required = isTemporaryCheck.checked;
            }
        }
        isTemporaryCheck.addEventListener('change', toggleReturnDate);
        toggleReturnDate();
    }
});
