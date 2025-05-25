document.addEventListener('DOMContentLoaded', function() {
    console.log('analisis_datos.js loaded');

    function showModal(modalId) {
        console.log('showModal called for:', modalId);
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        } else {
            console.error(modalId + ' element not found');
        }
    }

    function closeModal(modalId) {
        console.log('closeModal called for:', modalId);
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        } else {
            console.error(modalId + ' element not found');
        }
    }

    // Attach event listeners to buttons by ID
    var btnRegistrar = document.getElementById('btnRegistrar');
    if (btnRegistrar) {
        btnRegistrar.addEventListener('click', function() {
            showModal('platosModal');
        });
    }

    var btnVerHistorial = document.getElementById('btnVerHistorial');
    if (btnVerHistorial) {
        btnVerHistorial.addEventListener('click', function() {
            showModal('consumoModal');
        });
    }

    var btnVerDetalles = document.getElementById('btnVerDetalles');
    if (btnVerDetalles) {
        btnVerDetalles.addEventListener('click', function() {
            showModal('menuModal');
        });
    }

    var btnVerEstadisticas = document.getElementById('btnVerEstadisticas');
    if (btnVerEstadisticas) {
        btnVerEstadisticas.addEventListener('click', function() {
            showModal('distribucionModal');
        });
    }

    // Attach event listeners to close buttons
    var closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var modal = btn.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList && event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
});
