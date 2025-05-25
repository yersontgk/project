document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('.dashboard-grid a img');
    images.forEach(img => {
        img.style.opacity = 0;
        img.style.transition = 'opacity 0.6s ease-in-out';
        setTimeout(() => {
            img.style.opacity = 1;
        }, 100);
    });

    const slider = document.querySelector('.slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.slider-slide');
    const prevBtn = slider.querySelector('.slider-prev');
    const nextBtn = slider.querySelector('.slider-next');
    const sliderCharacter = slider.querySelector('.slider-character img');

    // Map section names to image filenames
    const sectionImages = {
        asistencia: 'Asistencia.png',
        platos_servidos: 'Platos.png',
        menu: 'Menus.png',
        gramaje: 'Gramaje.jpg',
        inventario: 'Inventario.png',
        reportes: 'Reportes.png',
        usuarios: 'Usuarios.png'
    };

    let currentIndex = 0;

    function updateSlider(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
                slide.style.opacity = '1';
                slide.style.pointerEvents = 'auto';
                slide.style.transform = 'translateX(0)';
            } else {
                slide.classList.remove('active');
                slide.style.opacity = '0';
                slide.style.pointerEvents = 'none';
                slide.style.transform = 'translateX(100%)';
            }
        });

        const activeSection = slides[index].getAttribute('data-section');
        if (sectionImages[activeSection]) {
            sliderCharacter.src = `../assets/imagenes/inicio/${sectionImages[activeSection]}`;
        }
    }

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateSlider(currentIndex);
    });

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % slides.length;
        updateSlider(currentIndex);
    });

    // Initialize slider
    updateSlider(currentIndex);
});
