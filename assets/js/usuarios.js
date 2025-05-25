function showCreateModal() {
    document.getElementById('createModal').style.display = 'block';
}

function showEditModal(id, nombre_completo, rol, estado) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nombre_completo').value = nombre_completo;
    document.getElementById('edit-rol').value = rol;
    document.getElementById('edit-estado').checked = estado;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
