document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-auto-filter]').forEach(function (form) {
        let debounceTimer;
        const departmentFilter = form.querySelector('[data-department-employee-filter]');
        const employeeFilter = form.querySelector('[data-department-filtered-employee]');

        function filterEmployeesByDepartment() {
            if (!departmentFilter || !employeeFilter) return;

            const departmentId = departmentFilter.value;
            const selectedEmployee = employeeFilter.selectedOptions[0];
            if (
                selectedEmployee &&
                selectedEmployee.value &&
                departmentId &&
                selectedEmployee.dataset.departmentId !== departmentId
            ) {
                employeeFilter.value = '';
            }

            Array.from(employeeFilter.options).slice(1).forEach(function (option) {
                option.hidden = Boolean(departmentId) && option.dataset.departmentId !== departmentId;
            });
        }

        filterEmployeesByDepartment();

        form.querySelectorAll('[data-debounce-filter]').forEach(function (input) {
            input.addEventListener('input', function () {
                window.clearTimeout(debounceTimer);
                debounceTimer = window.setTimeout(function () {
                    form.requestSubmit();
                }, 300);
            });
        });

        form.querySelectorAll('select, input[type="checkbox"], input[type="date"]').forEach(function (filter) {
            filter.addEventListener('change', function () {
                if (filter === departmentFilter) {
                    filterEmployeesByDepartment();
                }
                window.clearTimeout(debounceTimer);
                form.requestSubmit();
            });
        });
    });
});
