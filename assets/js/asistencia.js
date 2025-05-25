/* Removed all code related to 'btnRegistrarPlatos' and 'platosModal' */

// Unsaved changes modal logic
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('asistenciaForm');
    const modal = document.getElementById('unsavedChangesModal');
    const confirmBtn = document.getElementById('confirmExitBtn');
    const cancelBtn = document.getElementById('cancelExitBtn');

    let isFormDirty = false;
    let exitConfirmed = false;

    if (!form || !modal || !confirmBtn || !cancelBtn) return;

    // Mark form as dirty on input change
    form.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('change', () => {
            isFormDirty = true;
        });
    });

    // Reset dirty flag on form submit
    form.addEventListener('submit', () => {
        isFormDirty = false;
    });

    // Show modal on beforeunload if form is dirty
    window.addEventListener('beforeunload', function(e) {
        if (isFormDirty && !exitConfirmed) {
            e.preventDefault();
            e.returnValue = '';
            modal.style.display = 'block';
            return '';
        }
    });

    // Intercept navigation clicks (e.g., sidebar links)
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (isFormDirty && !exitConfirmed) {
                e.preventDefault();
                modal.style.display = 'block';

                confirmBtn.onclick = () => {
                    exitConfirmed = true;
                    modal.style.display = 'none';
                    window.location.href = this.href;
                };

                cancelBtn.onclick = () => {
                    modal.style.display = 'none';
                };
            }
        });
    });

    // Modal buttons
    confirmBtn.onclick = () => {
        exitConfirmed = true;
        modal.style.display = 'none';
        window.close();
    };

    cancelBtn.onclick = () => {
        modal.style.display = 'none';
    };
});

document.getElementById('tipoSelect').addEventListener('change', function() {
    var tipo = this.value;
    var gradoGroup = document.getElementById('gradoGroup');
    var gradoSelect = document.getElementById('gradoSelect');
    var rows = document.querySelectorAll('#asistenciaTable tbody tr');

    if (tipo === 'estudiante') {
        gradoGroup.style.display = 'block';
    } else {
        gradoGroup.style.display = 'none';
        gradoSelect.value = '';
    }

    rows.forEach(function(row) {
        if (tipo === '') {
            row.style.display = '';
        } else if (tipo === 'estudiante') {
            var grado = gradoSelect.value;
            if (grado === '') {
                row.style.display = row.getAttribute('data-tipo') === tipo ? '' : 'none';
            } else {
                row.style.display = (row.getAttribute('data-tipo') === tipo && row.getAttribute('data-grado') === grado) ? '' : 'none';
            }
        } else {
            row.style.display = row.getAttribute('data-tipo') === tipo ? '' : 'none';
        }
    });
});

// Trigger change event on page load to apply filters
document.addEventListener('DOMContentLoaded', function() {
    var tipoSelect = document.getElementById('tipoSelect');
    if (tipoSelect) {
        var event = new Event('change');
        tipoSelect.dispatchEvent(event);
    }
});

document.getElementById('gradoSelect').addEventListener('change', function() {
    var grado = this.value;
    var tipo = document.getElementById('tipoSelect').value;
    var rows = document.querySelectorAll('#asistenciaTable tbody tr');

    rows.forEach(function(row) {
        if (grado === '') {
            row.style.display = row.getAttribute('data-tipo') === tipo ? '' : 'none';
        } else {
            row.style.display = (row.getAttribute('data-tipo') === tipo && row.getAttribute('data-grado') === grado) ? '' : 'none';
        }
    });
});

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modales cuando se hace clic fuera de ellos
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="number"]');
    
    function updateTotals() {
        let totalMasculino = 0;
        let totalFemenino = 0;

        document.querySelectorAll('#asistenciaTable tbody tr').forEach(row => {
            const masculino = parseInt(row.querySelector('input[name*="[masculino]"]').value) || 0;
            const femenino = parseInt(row.querySelector('input[name*="[femenino]"]').value) || 0;
            const total = masculino + femenino;
            
            row.querySelector('.total-row').textContent = total;
            totalMasculino += masculino;
            totalFemenino += femenino;
        });

        document.getElementById('total-masculino').textContent = totalMasculino;
        document.getElementById('total-femenino').textContent = totalFemenino;
        document.getElementById('total-general').textContent = totalMasculino + totalFemenino;
    }

    inputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    updateTotals();
});

function editarMatricula(id, grado, seccion) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-grado').value = grado;
    document.getElementById('edit-seccion').value = seccion;
    document.getElementById('editModal').style.display = 'block';
}

function editarLimites(id, limite_masculino, limite_femenino) {
    document.getElementById('limites-id').value = id;
    document.getElementById('limite_masculino').value = limite_masculino;
    document.getElementById('limite_femenino').value = limite_femenino;
    document.getElementById('limitesModal').style.display = 'block';
}

document.getElementById('btnNuevaMatricula').addEventListener('click', function() {
    document.getElementById('nuevaMatriculaModal').style.display = 'block';
});

document.getElementById('tipo').addEventListener('change', function() {
    var tipo = this.value;
    var camposEstudiante = document.getElementById('camposEstudiante');
    var gradoGroup = document.getElementById('gradoGroup');
    var seccionGroup = document.getElementById('seccionGroup');
    var lapsoGroup = document.getElementById('lapsoGroup');

    if (tipo === 'estudiante') {
        camposEstudiante.style.display = 'block';
        gradoGroup.style.display = 'block';
        seccionGroup.style.display = 'block';
        lapsoGroup.style.display = 'block';
    } else if (tipo === 'docente') {
        camposEstudiante.style.display = 'block';
        gradoGroup.style.display = 'none';
        seccionGroup.style.display = 'none';
        lapsoGroup.style.display = 'block';
        document.getElementById('grado').value = '';
        document.getElementById('seccion').value = '';
    } else if (tipo === 'otros') {
        camposEstudiante.style.display = 'block';
        gradoGroup.style.display = 'none';
        seccionGroup.style.display = 'none';
        lapsoGroup.style.display = 'none';
        document.getElementById('grado').value = '';
        document.getElementById('seccion').value = '';
        document.getElementById('lapso_academico').value = '';
    } else {
        camposEstudiante.style.display = 'none';
        document.getElementById('grado').value = '';
        document.getElementById('seccion').value = '';
        document.getElementById('lapso_academico').value = '';
    }
});

// Trigger change event on page load to set correct fields visibility
document.addEventListener('DOMContentLoaded', function() {
    var tipoSelect = document.getElementById('tipo');
    if (tipoSelect) {
        var event = new Event('change');
        tipoSelect.dispatchEvent(event);
    }
});

// Close modal function
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target && event.target.nodeType === 1 && event.target.classList && event.target.classList.contains('modal') && event.target.style) {
        event.target.style.display = 'none';
    }
}
