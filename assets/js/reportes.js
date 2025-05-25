// Función para cambiar entre pestañas
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Remover clase active de todos los botones y secciones
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.report-section').forEach(section => section.classList.remove('active'));
        
        // Agregar clase active al botón clickeado y su sección correspondiente
        button.classList.add('active');
        document.getElementById(button.dataset.tab).classList.add('active');
    });
});

// Datos para los gráficos
const datosAsistencia = window.datosAsistencia || [];
const datosConsumo = window.datosConsumo || [];

// Configuración de gráficos
const resumenChart = new Chart(document.getElementById('resumenChart'), {
    type: 'line',
    data: {
        labels: datosAsistencia.map(d => d.fecha),
        datasets: [{
            label: 'Asistencia Total',
            data: datosAsistencia.map(d => d.total),
            borderColor: '#10a37f',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Asistencia Diaria'
            }
        }
    }
});

const asistenciaChart = new Chart(document.getElementById('asistenciaChart'), {
    type: 'bar',
    data: {
        labels: datosAsistencia.map(d => d.fecha),
        datasets: [{
            label: 'Estudiantes',
            data: datosAsistencia.filter(d => d.tipo === 'estudiante').map(d => d.total),
            backgroundColor: '#10a37f'
        }, {
            label: 'Docentes',
            data: datosAsistencia.filter(d => d.tipo === 'docente').map(d => d.total),
            backgroundColor: '#2563eb'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Asistencia por Tipo'
            }
        }
    }
});

const consumoChart = new Chart(document.getElementById('consumoChart'), {
    type: 'bar',
    data: {
        labels: datosConsumo.map(d => d.producto),
        datasets: [{
            label: 'Consumo Total',
            data: datosConsumo.map(d => d.total),
            backgroundColor: '#10a37f'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Consumo por Producto'
            }
        }
    }
});

// Funciones de exportación
function exportToPDF() {
    const element = document.querySelector('.report-container');
    if (!element) return;

    const charts = [resumenChart, asistenciaChart, consumoChart];
    const canvasElements = charts.map(chart => chart.canvas);
    const imgElements = [];

    canvasElements.forEach((canvas, index) => {
        const img = document.createElement('img');
        img.src = charts[index].toBase64Image();
        img.style.width = canvas.style.width || canvas.width + 'px';
        img.style.height = canvas.style.height || canvas.height + 'px';
        canvas.parentNode.replaceChild(img, canvas);
        imgElements.push({img, canvas});
    });

    const opt = {
        margin: [-5,0,0,0],
        filename: 'reporte.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        imgElements.forEach(({img, canvas}) => {
            img.parentNode.replaceChild(canvas, img);
        });
    }).catch(() => {
        imgElements.forEach(({img, canvas}) => {
            img.parentNode.replaceChild(canvas, img);
        });
    });
}

function exportToExcel() {
    alert('Funcionalidad de exportar a Excel no implementada.');
}

// Unsaved changes modal logic
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
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
