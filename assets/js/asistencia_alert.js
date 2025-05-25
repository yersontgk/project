document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('asistenciaForm');
    let isFormDirty = false;
    let exitConfirmed = false;

    // Create modal elements dynamically
    const modal = document.createElement('div');
    modal.id = 'unsavedChangesModal';
    Object.assign(modal.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0,0,0,0.4)',
        display: 'none',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: '1050',
    });

    const modalContent = document.createElement('div');
    Object.assign(modalContent.style, {
        backgroundColor: 'white',
        border: '3px solid #ef4444',
        borderRadius: '10px',
        padding: '20px',
        width: '400px',
        maxWidth: '90%',
        boxShadow: '0 0 15px rgba(239, 68, 68, 0.5)',
        textAlign: 'center',
        position: 'relative',
    });

    const iconDiv = document.createElement('div');
    iconDiv.textContent = '⚠️';
    Object.assign(iconDiv.style, {
        fontSize: '48px',
        color: '#facc15',
        marginBottom: '15px',
    });

    const title = document.createElement('h2');
    title.textContent = 'Advertencia de Seguridad';
    Object.assign(title.style, {
        fontSize: '1.5rem',
        fontWeight: '700',
        color: '#dc2626',
        marginBottom: '10px',
    });

    const message = document.createElement('p');
    message.textContent = 'Hay cambios sin guardar. ¿Seguro que quieres salir?';
    Object.assign(message.style, {
        fontSize: '1rem',
        marginBottom: '20px',
        color: '#374151',
    });

    const buttonsDiv = document.createElement('div');
    Object.assign(buttonsDiv.style, {
        display: 'flex',
        justifyContent: 'center',
        gap: '10px',
    });

    const confirmBtn = document.createElement('button');
    confirmBtn.textContent = 'Confirmar';
    Object.assign(confirmBtn.style, {
        backgroundColor: '#059669',
        color: 'white',
        border: 'none',
        padding: '10px 20px',
        borderRadius: '5px',
        cursor: 'pointer',
        fontWeight: '600',
    });
    confirmBtn.addEventListener('mouseenter', () => {
        confirmBtn.style.backgroundColor = '#047857';
    });
    confirmBtn.addEventListener('mouseleave', () => {
        confirmBtn.style.backgroundColor = '#059669';
    });

    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancelar';
    Object.assign(cancelBtn.style, {
        backgroundColor: '#f3f4f6',
        color: '#111827',
        border: 'none',
        padding: '10px 20px',
        borderRadius: '5px',
        cursor: 'pointer',
        fontWeight: '600',
    });
    cancelBtn.addEventListener('mouseenter', () => {
        cancelBtn.style.backgroundColor = '#e5e7eb';
    });
    cancelBtn.addEventListener('mouseleave', () => {
        cancelBtn.style.backgroundColor = '#f3f4f6';
    });

    buttonsDiv.appendChild(confirmBtn);
    buttonsDiv.appendChild(cancelBtn);

    modalContent.appendChild(iconDiv);
    modalContent.appendChild(title);
    modalContent.appendChild(message);
    modalContent.appendChild(buttonsDiv);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Mark form as dirty on input change
    if (!form) return;
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
            modal.style.display = 'flex';
            return '';
        }
    });

    // Intercept navigation clicks (e.g., sidebar links)
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (isFormDirty && !exitConfirmed) {
                e.preventDefault();
                modal.style.display = 'flex';

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
    // Remove the conflicting confirmBtn.onclick that calls window.close()
    // The confirmBtn.onclick inside link click event will handle navigation

    cancelBtn.onclick = () => {
        modal.style.display = 'none';
    };
});
