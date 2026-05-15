// Cambio: mueve la logica del modal de reseña a un JS externo reutilizable.
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('writereview');
    if (!modalElement || !window.bootstrap) return;

    // Cambio: abre el modal automaticamente cuando la vista indica errores de validacion.
    const shouldOpen = modalElement.dataset.openOnLoad === '1';
    if (shouldOpen) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
});