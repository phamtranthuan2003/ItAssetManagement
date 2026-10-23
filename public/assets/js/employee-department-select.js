/**
 * Searchable employee dropdowns linked to department selects.
 */
document.addEventListener('DOMContentLoaded', function () {
    const widgets = [];

    document.querySelectorAll('[data-employee-search-select]').forEach(function (wrapper) {
        const employeeSelect = wrapper.querySelector('[data-employee-select]');
        const departmentSelect = document.getElementById(wrapper.dataset.departmentSelectId);
        const toggle = wrapper.querySelector('[data-employee-select-toggle]');
        const menu = wrapper.querySelector('[data-employee-select-menu]');
        const searchInput = wrapper.querySelector('[data-employee-search-input]');
        const optionsContainer = wrapper.querySelector('[data-employee-select-options]');
        const noResults = wrapper.querySelector('[data-employee-no-results]');

        if (!employeeSelect || !toggle || !menu || !searchInput || !optionsContainer || !noResults) {
            return;
        }

        const options = Array.from(employeeSelect.options).slice(1);
        const widget = { employeeSelect, menu, searchInput, toggle, options };
        widgets.push(widget);

        function isOptionVisible(option) {
            const departmentMatches = !departmentSelect || !departmentSelect.value ||
                option.dataset.departmentId === departmentSelect.value;
            const query = searchInput.value.trim().toLocaleLowerCase();
            return departmentMatches && (!query || option.textContent.toLocaleLowerCase().includes(query));
        }

        function renderOptions() {
            optionsContainer.replaceChildren();
            let visibleCount = 0;

            options.forEach(function (option) {
                if (!isOptionVisible(option)) {
                    return;
                }

                visibleCount += 1;
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'dropdown-item text-wrap';
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', employeeSelect.value === option.value ? 'true' : 'false');
                item.textContent = option.textContent.trim();
                item.addEventListener('click', function () {
                    employeeSelect.value = option.value;
                    employeeSelect.dispatchEvent(new Event('change', { bubbles: true }));
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
        widget.openMenu = openMenu;

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

        if (departmentSelect) {
            departmentSelect.addEventListener('change', function () {
                employeeSelect.value = '';
                toggle.textContent = employeeSelect.options[0].textContent;
                searchInput.value = '';
                searchInput.setCustomValidity('');
                renderOptions();
            });
        }

        document.addEventListener('click', function (event) {
            if (!wrapper.contains(event.target)) {
                closeMenu();
            }
        });

        const selectedOption = employeeSelect.selectedOptions[0];
        toggle.textContent = selectedOption ? selectedOption.textContent.trim() : employeeSelect.options[0].textContent;
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const requiredWidget = widgets.find(function (widget) {
                return form.contains(widget.employeeSelect) &&
                    widget.employeeSelect.dataset.required === '1' &&
                    !widget.employeeSelect.value;
            });

            if (!requiredWidget) {
                return;
            }

            event.preventDefault();
            requiredWidget.searchInput.setCustomValidity('Vui lòng chọn nhân viên.');
            requiredWidget.openMenu();
            requiredWidget.searchInput.reportValidity();
        });
    });
});
