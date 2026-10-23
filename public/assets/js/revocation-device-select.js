document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('deviceRowsContainer');
    const employeeSelect = document.getElementById('fromEmployeeSelect');
    const departmentSelect = document.getElementById('fromDepartmentSelect');
    const form = document.getElementById('ticketForm');

    if (!container || !employeeSelect || !form) {
        return;
    }

    function renderOptions(wrapper) {
        const select = wrapper.querySelector('[data-device-select]');
        const searchInput = wrapper.querySelector('[data-device-search-input]');
        const optionsContainer = wrapper.querySelector('[data-device-select-options]');
        const noResults = wrapper.querySelector('[data-device-no-results]');
        const query = searchInput.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        optionsContainer.replaceChildren();
        Array.from(select.options).slice(1).forEach(function (option) {
            if (option.hidden || (query && !option.textContent.toLocaleLowerCase().includes(query))) {
                return;
            }

            visibleCount += 1;
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'dropdown-item text-wrap';
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', select.value === option.value ? 'true' : 'false');
            item.dataset.deviceOptionValue = option.value;
            item.textContent = option.textContent.trim();
            optionsContainer.appendChild(item);
        });

        noResults.classList.toggle('d-none', visibleCount > 0);
    }

    function closeMenu(wrapper) {
        wrapper.querySelector('[data-device-select-menu]').classList.remove('show');
        wrapper.querySelector('[data-device-select-toggle]').setAttribute('aria-expanded', 'false');
    }

    function openMenu(wrapper) {
        wrapper.querySelector('[data-device-select-menu]').classList.add('show');
        wrapper.querySelector('[data-device-select-toggle]').setAttribute('aria-expanded', 'true');
        renderOptions(wrapper);
        wrapper.querySelector('[data-device-search-input]').focus();
    }

    function initializeRow(wrapper) {
        const employeeId = employeeSelect.value;
        const select = wrapper.querySelector('[data-device-select]');
        const toggle = wrapper.querySelector('[data-device-select-toggle]');
        const searchInput = wrapper.querySelector('[data-device-search-input]');

        select.value = '';
        toggle.textContent = employeeId ? '-- Chọn thiết bị --' : '-- Chọn nhân viên trước --';
        searchInput.value = '';
        searchInput.setCustomValidity('');

        Array.from(select.options).slice(1).forEach(function (option) {
            option.hidden = !employeeId || option.dataset.employeeId !== employeeId;
        });

        renderOptions(wrapper);
        closeMenu(wrapper);
    }

    function filterDevicesForEmployee() {
        container.querySelectorAll('[data-revocation-device-select]').forEach(initializeRow);
    }

    container.addEventListener('click', function (event) {
        const toggle = event.target.closest('[data-device-select-toggle]');
        if (toggle) {
            const wrapper = toggle.closest('[data-revocation-device-select]');
            if (wrapper.querySelector('[data-device-select-menu]').classList.contains('show')) {
                closeMenu(wrapper);
            } else {
                openMenu(wrapper);
            }
            return;
        }

        const item = event.target.closest('[data-device-option-value]');
        if (item) {
            const wrapper = item.closest('[data-revocation-device-select]');
            const select = wrapper.querySelector('[data-device-select]');
            const option = Array.from(select.options).find(function (selectOption) {
                return selectOption.value === item.dataset.deviceOptionValue;
            });

            select.value = option.value;
            wrapper.querySelector('[data-device-select-toggle]').textContent = option.textContent.trim();
            wrapper.querySelector('[data-device-search-input]').value = '';
            wrapper.querySelector('[data-device-search-input]').setCustomValidity('');
            closeMenu(wrapper);
        }
    });

    container.addEventListener('input', function (event) {
        if (event.target.matches('[data-device-search-input]')) {
            event.target.setCustomValidity('');
            renderOptions(event.target.closest('[data-revocation-device-select]'));
        }
    });

    container.addEventListener('keydown', function (event) {
        if (event.target.matches('[data-device-search-input]') && event.key === 'Escape') {
            const wrapper = event.target.closest('[data-revocation-device-select]');
            closeMenu(wrapper);
            wrapper.querySelector('[data-device-select-toggle]').focus();
        }
    });

    document.addEventListener('click', function (event) {
        container.querySelectorAll('[data-revocation-device-select]').forEach(function (wrapper) {
            if (!wrapper.contains(event.target)) {
                closeMenu(wrapper);
            }
        });
    });

    employeeSelect.addEventListener('change', filterDevicesForEmployee);
    departmentSelect.addEventListener('change', function () {
        setTimeout(filterDevicesForEmployee, 0);
    });
    container.addEventListener('revocation-device-row-added', function (event) {
        initializeRow(event.detail.querySelector('[data-revocation-device-select]'));
    });

    form.addEventListener('submit', function (event) {
        const missingSelection = Array.from(container.querySelectorAll('[data-revocation-device-select]')).find(function (wrapper) {
            return !wrapper.querySelector('[data-device-select]').value;
        });

        if (!missingSelection) {
            return;
        }

        event.preventDefault();
        const searchInput = missingSelection.querySelector('[data-device-search-input]');
        searchInput.setCustomValidity('Vui lòng chọn thiết bị của nhân viên đã chọn.');
        openMenu(missingSelection);
        searchInput.reportValidity();
    });

    filterDevicesForEmployee();
});
