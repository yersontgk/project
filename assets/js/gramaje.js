// JavaScript para cálculos de gramaje y manejo del botón "Restar insumos"

document.addEventListener('DOMContentLoaded', function() {
    const restarInsumosBtn = document.getElementById('restar-insumos-btn');
    const warningModal = document.getElementById('warning-modal');
    const warningMessage = document.getElementById('warning-message');
    const warningConfirmBtn = document.getElementById('warning-confirm-btn');
    const warningCancelBtn = document.getElementById('warning-cancel-btn');
    const warningCloseBtn = document.getElementById('warning-close-btn');

    function showWarningModal(message, isSuccess = false) {
        warningMessage.textContent = message;
        const modalIcon = document.getElementById('modal-icon');
        if (isSuccess) {
            modalIcon.textContent = '✅';
            modalIcon.classList.remove('warning-icon');
            modalIcon.classList.add('success-icon');
        } else {
            modalIcon.textContent = '⚠️';
            modalIcon.classList.remove('success-icon');
            modalIcon.classList.add('warning-icon');
        }
        warningModal.style.display = 'block';
    }

    const successModal = document.getElementById('success-modal');
    const successMessage = document.getElementById('success-message');
    const successCloseBtn = document.getElementById('success-close-btn');

    function showSuccessModal(message) {
        successMessage.textContent = message;
        successModal.style.display = 'block';
    }

    function hideSuccessModal() {
        successModal.style.display = 'none';
    }

    successCloseBtn.addEventListener('click', function() {
        hideSuccessModal();
    });

    function hideWarningModal() {
        warningModal.style.display = 'none';
    }

    if (restarInsumosBtn) {
        restarInsumosBtn.addEventListener('click', function() {
            // Check if any product is low stock
            const productoRows = document.querySelectorAll('.producto-row');
            let lowStock = false;
            productoRows.forEach(row => {
                if (row.getAttribute('data-low-stock') === 'true') {
                    lowStock = true;
                }
            });

            if (lowStock) {
                // Show warning modal
                showWarningModal('¿Está seguro de que desea restar el total necesario de cada producto en el inventario?');
            } else {
                // Show friendly modal
                showFriendlyModal('El inventario está en niveles normales. ¿Desea continuar con la operación?');
            }
        });

        warningConfirmBtn.addEventListener('click', function() {
            hideWarningModal();
            proceedRestarInsumos();
        });

        warningCancelBtn.addEventListener('click', function() {
            hideWarningModal();
        });
    }

    const friendlyModal = document.getElementById('friendly-modal');
    const friendlyMessage = document.getElementById('friendly-message');
    const friendlyConfirmBtn = document.getElementById('friendly-confirm-btn');
    const friendlyCloseBtn = document.getElementById('friendly-close-btn');

    function showFriendlyModal(message) {
        friendlyMessage.textContent = message;
        friendlyModal.style.display = 'block';
    }

    function hideFriendlyModal() {
        friendlyModal.style.display = 'none';
    }

    friendlyCloseBtn.addEventListener('click', function() {
        hideFriendlyModal();
    });

    friendlyConfirmBtn.addEventListener('click', function() {
        hideFriendlyModal();
        proceedRestarInsumos();
    });

    // Remove warningConfirmBtn event listener as Confirm button is removed
    // Instead, proceed with action immediately or after user closes warning modal

    warningCloseBtn.addEventListener('click', function() {
        hideWarningModal();
        proceedRestarInsumos();
    });

    function proceedRestarInsumos() {
        const fechaInput = document.getElementById('fecha');
        const fecha = fechaInput ? fechaInput.value : '';

        fetch('../../controllers/gramaje_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'restarInsumos',
                fecha: fecha
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            if (!text) {
                throw new Error('Empty response');
            }
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response');
            }
        })
        .then(data => {
            if (data.success) {
                showSuccessModal('Se han restado los insumos correctamente.');
                setTimeout(() => {
                    hideSuccessModal();
                    window.location.reload();
                }, 3000);
            } else {
                showWarningModal('Error al restar los insumos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            showWarningModal('Error en la petición: ' + error.message);
        });
    }
});
