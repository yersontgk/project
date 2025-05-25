document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('platosForm');
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

    // Add event listener for tipo select to reload page with selected tipo and fecha
    const tipoSelect = document.getElementById('tipo');
    if (tipoSelect) {
        tipoSelect.addEventListener('change', function() {
            const selectedTipo = this.value;
            const fechaInput = document.getElementById('fecha');
            const selectedFecha = fechaInput ? fechaInput.value : '';
            const url = new URL(window.location.href);
            url.searchParams.set('tipo', selectedTipo);
            if (selectedFecha) {
                url.searchParams.set('fecha', selectedFecha);
            }
            // Remove grado param if tipo is not estudiante
            if (selectedTipo !== 'estudiante') {
                url.searchParams.delete('grado');
            }
            window.location.href = url.toString();
        });
    }
});
