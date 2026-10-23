document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-auto-filter]').forEach(function (form) {
        let debounceTimer;

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
                window.clearTimeout(debounceTimer);
                form.requestSubmit();
            });
        });
    });
});
