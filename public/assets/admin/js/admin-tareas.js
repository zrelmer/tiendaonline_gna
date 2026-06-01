(function () {
    'use strict';

    function initAdminTareas() {
        var checks = document.querySelectorAll('.admin-task-check');

        checks.forEach(function (checkbox) {
            var taskId = checkbox.getAttribute('data-admin-task');

            if (!taskId) {
                return;
            }

            var storageKey = 'admin_tarea_' + taskId;
            checkbox.checked = localStorage.getItem(storageKey) === '1';

            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    localStorage.setItem(storageKey, '1');
                } else {
                    localStorage.removeItem(storageKey);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminTareas);
    } else {
        initAdminTareas();
    }
})();
