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

    // 3. Bật/tắt chọn hạn trả khi check Cấp phát tạm thời
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

    // Search and select devices from the maintenance dropdown.
    document.querySelectorAll('[data-maintenance-device-picker]').forEach(function (picker) {
        const deviceSelect = picker.querySelector('[data-maintenance-device-select]');
        const toggle = picker.querySelector('[data-maintenance-device-toggle]');
        const menu = picker.querySelector('[data-maintenance-device-menu]');
        const searchInput = picker.querySelector('[data-maintenance-device-search]');
        const optionsContainer = picker.querySelector('[data-maintenance-device-options]');
        const noResults = picker.querySelector('[data-maintenance-device-no-results]');

        if (!deviceSelect || !toggle || !menu || !searchInput || !optionsContainer || !noResults) {
            return;
        }

        const options = Array.from(deviceSelect.options).slice(1);
        const placeholder = deviceSelect.options[0].textContent.trim();
        deviceSelect.classList.add('d-none');
        deviceSelect.required = false;
        toggle.classList.remove('d-none');

        function renderOptions() {
            optionsContainer.replaceChildren();
            const query = searchInput.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            options.forEach(function (option) {
                if (!option.textContent.toLocaleLowerCase().includes(query)) {
                    return;
                }

                visibleCount += 1;
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'dropdown-item text-wrap';
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', deviceSelect.value === option.value ? 'true' : 'false');
                item.textContent = option.textContent.trim();
                item.addEventListener('click', function () {
                    deviceSelect.value = option.value;
                    deviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    toggle.textContent = option.textContent.trim();
                    searchInput.setCustomValidity('');
                    closeMenu();
                    toggle.focus();
                });
                optionsContainer.appendChild(item);
            });

            noResults.classList.toggle('d-none', visibleCount > 0);
        }

        function openMenu() {
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            renderOptions();
            searchInput.focus();
        }

        function closeMenu() {
            menu.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.textContent = deviceSelect.selectedOptions[0].value
            ? deviceSelect.selectedOptions[0].textContent.trim()
            : placeholder;
        toggle.addEventListener('click', function () {
            if (menu.classList.contains('show')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
        searchInput.addEventListener('input', renderOptions);
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
                toggle.focus();
            }
        });
        document.addEventListener('click', function (event) {
            if (!picker.contains(event.target)) {
                closeMenu();
            }
        });

        const form = deviceSelect.closest('form');
        if (form) {
            form.addEventListener('submit', function (event) {
                if (deviceSelect.value) {
                    return;
                }

                event.preventDefault();
                searchInput.setCustomValidity('Vui lòng chọn thiết bị.');
                openMenu();
                searchInput.reportValidity();
            });
        }
    });
});
