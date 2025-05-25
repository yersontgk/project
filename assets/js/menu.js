function showCreateModal() {
    console.log('showCreateModal called');
    document.getElementById('createModal').style.display = 'block';
    document.getElementById('fecha').value = new Date().toISOString().split('T')[0];
    // calcularGramajeTotal(); // Commented out to fix missing function error
    // Call initializeProductSelectionCreate after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeProductSelectionCreate();
    }, 100);
}

function showEditModal(id, nombre, observacion, fecha, productos) {
    console.log('showEditModal called');
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-observacion').value = observacion;
    document.getElementById('edit-fecha').value = fecha
    
    // Resetear todos los inputs de productos
    document.querySelectorAll('.producto-cantidad-edit').forEach(input => {
        input.value = 0;
    });

    // Establecer las cantidades de los productos existentes
    for (let id_producto in productos) {
        const input = document.querySelector(`input[name="productos[${id_producto}]"].producto-cantidad-edit`);
        if (input) {
            input.value = productos[id_producto];
        }
    }

    initializeProductSelectionEdit(productos);

    document.getElementById('editModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modales cuando se hace clic fuera de ellos
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}

/* Removed all console.log statements as per user request */

// Toggle checklist visibility for create modal
const toggleCreateBtn = document.getElementById('toggleProductListCreate');
if (toggleCreateBtn) {
    toggleCreateBtn.addEventListener('click', function() {
        console.log('Toggle product checklist create clicked');
        const checklist = document.getElementById('productChecklistCreate');
        if (checklist.style.display === 'none' || checklist.style.display === '') {
            checklist.style.display = 'block';
            this.textContent = '➖ Ocultar productos';
        } else {
            checklist.style.display = 'none';
            this.textContent = '➕ Mostrar productos';
        }
    });
}

// Toggle checklist visibility for edit modal
const toggleEditBtn = document.getElementById('toggleProductListEdit');
if (toggleEditBtn) {
    toggleEditBtn.addEventListener('click', function() {
        const checklist = document.getElementById('productChecklistEdit');
        if (checklist.style.display === 'none' || checklist.style.display === '') {
            checklist.style.display = 'block';
            this.textContent = '➖ Ocultar productos';
        } else {
            checklist.style.display = 'none';
            this.textContent = '➕ Mostrar productos';
        }
    });
}

// Initialize product selection for create modal
function initializeProductSelectionCreate() {
    console.log('initializeProductSelectionCreate called');
    const selectedProductsContainer = document.getElementById('selectedProductsCreate');
    const checkboxes = document.querySelectorAll('.product-checkbox-create');
    console.log('Found checkboxes:', checkboxes.length);
    selectedProductsContainer.innerHTML = '';

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        // Remove any existing event listeners by cloning the node
        const newCheckbox = checkbox.cloneNode(true);
        checkbox.parentNode.replaceChild(newCheckbox, checkbox);

        // Attach event listener with debugging log
        newCheckbox.addEventListener('change', () => {
            console.log('Checkbox changed:', newCheckbox.id, 'Checked:', newCheckbox.checked);
            updateSelectedProductsCreate();
        });
    });

    // Call updateSelectedProductsCreate initially to clear or show selected products
    updateSelectedProductsCreate();
}

// Update selected products display for create modal
function updateSelectedProductsCreate() {
    console.log('updateSelectedProductsCreate called');
    const selectedProductsContainer = document.getElementById('selectedProductsCreate');
    const checkboxes = document.querySelectorAll('.product-checkbox-create');
    console.log('Checkboxes count in update:', checkboxes.length);
    selectedProductsContainer.innerHTML = '';

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const productId = checkbox.value;
            const productLabel = document.querySelector(`label[for="check-create-${productId}"]`).textContent;
            console.log('Checkbox checked:', productId, productLabel);

            // Create product card
            const card = document.createElement('div');
            card.className = 'producto-card';

            const title = document.createElement('h5');
            title.textContent = productLabel;
            card.appendChild(title);

            const formGroup = document.createElement('div');
            formGroup.className = 'form-group';
            formGroup.style.display = 'flex';
            formGroup.style.alignItems = 'center';

            const label = document.createElement('label');
            label.style.marginRight = '0.5rem';
            label.textContent = 'Cantidad por plato:';
            formGroup.appendChild(label);

            const input = document.createElement('input');
            input.type = 'number';
            input.name = `productos[${productId}]`;
            input.className = 'form-control producto-cantidad';
            input.value = 0;
            input.min = 0;
            input.step = 0.01;
            input.style.width = '80px';
            input.style.marginRight = '0.3rem';
            formGroup.appendChild(input);

            // Add unit span if applicable
            const unitText = productLabel.match(/\(([^)]+)\)$/);
            if (unitText && (unitText[1].includes('kg') || unitText[1].includes('g'))) {
                const span = document.createElement('span');
                span.textContent = 'g';
                formGroup.appendChild(span);
            }

            card.appendChild(formGroup);
            selectedProductsContainer.appendChild(card);
        }
    });
}

// Initialize product selection for edit modal
function initializeProductSelectionEdit(selectedProducts) {
    const selectedProductsContainer = document.getElementById('selectedProductsEdit');
    const checkboxes = document.querySelectorAll('.product-checkbox-edit');
    selectedProductsContainer.innerHTML = '';

    checkboxes.forEach(checkbox => {
        const productId = checkbox.value;
        if (selectedProducts.hasOwnProperty(productId) && selectedProducts[productId] > 0) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
        checkbox.addEventListener('change', () => {
            updateSelectedProductsEdit();
        });
    });

    updateSelectedProductsEdit();
}

// Update selected products display for edit modal
function updateSelectedProductsEdit() {
    const selectedProductsContainer = document.getElementById('selectedProductsEdit');
    const checkboxes = document.querySelectorAll('.product-checkbox-edit');
    selectedProductsContainer.innerHTML = '';

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const productId = checkbox.value;
            const productLabel = document.querySelector(`label[for="check-edit-${productId}"]`).textContent;

            // Create product card
            const card = document.createElement('div');
            card.className = 'producto-card';

            const title = document.createElement('h5');
            title.textContent = productLabel;
            card.appendChild(title);

            const formGroup = document.createElement('div');
            formGroup.className = 'form-group';
            formGroup.style.display = 'flex';
            formGroup.style.alignItems = 'center';

            const label = document.createElement('label');
            label.style.marginRight = '0.5rem';
            label.textContent = 'Cantidad por plato:';
            formGroup.appendChild(label);

            const input = document.createElement('input');
            input.type = 'number';
            input.name = `productos[${productId}]`;
            input.className = 'form-control producto-cantidad-edit';
            input.value = 0;
            input.min = 0;
            input.step = 0.01;
            input.style.width = '80px';
            input.style.marginRight = '0.3rem';
            formGroup.appendChild(input);

            // Add unit span if applicable
            const unitText = productLabel.match(/\(([^)]+)\)$/);
            if (unitText && (unitText[1].includes('kg') || unitText[1].includes('g'))) {
                const span = document.createElement('span');
                span.textContent = 'g';
                formGroup.appendChild(span);
            }

            card.appendChild(formGroup);
            selectedProductsContainer.appendChild(card);
        }
    });
}

// Toggle disabled menus table visibility
const toggleDisabledMenusBtn = document.getElementById('toggleDisabledMenusBtn');
const disabledMenusContainer = document.getElementById('disabledMenusContainer');

if (toggleDisabledMenusBtn && disabledMenusContainer) {
    toggleDisabledMenusBtn.addEventListener('click', () => {
        if (disabledMenusContainer.style.display === 'none' || disabledMenusContainer.style.display === '') {
            disabledMenusContainer.style.display = 'block';
            toggleDisabledMenusBtn.textContent = 'Ocultar Menús Deshabilitados';
        } else {
            disabledMenusContainer.style.display = 'none';
            toggleDisabledMenusBtn.textContent = 'Mostrar Menús Deshabilitados';
        }
    });
}
