document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slider-slide');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const btnLinks = document.querySelectorAll('.btn-link');
    const backgroundCirclesContainer = document.querySelector('.background-circles');
    const illuminationOverlay = document.querySelector('.illumination-overlay');
    const dotsContainer = document.querySelector('.slider-dots');
    const sliderCharacterImg = document.querySelector('.slider-character img');

    let currentIndex = 0;
    let isAnimating = false;
    let autoSlideInterval;

    // Create background circles elements
    function createCircles() {
        const circle1 = document.createElement('div');
        circle1.classList.add('circle', 'circle1');
        const circle2 = document.createElement('div');
        circle2.classList.add('circle', 'circle2');
        backgroundCirclesContainer.appendChild(circle1);
        backgroundCirclesContainer.appendChild(circle2);
    }

    createCircles();

    // Updated positions for circles per section with stronger position changes
    const circlePositions = {
        asistencia: [{top: '10%', left: '10%'}, {top: '80%', left: '80%'}],
        platos_servidos: [{top: '50%', left: '40%'}, {top: '20%', left: '80%'}],
        menu: [{top: '80%', left: '25%'}, {top: '-3%', left: '90%'}],
        gramaje: [{top: '10%', left: '50%'}, {top: '0%', left: '-5%'}],
        inventario: [{top: '60%', left: '80%'}, {top: '10%', left: '10%'}],
        reportes: [{top: '-5%', left: '-5%'}, {top: '50%', left: '50%'}],
        usuarios: [{top: '70%', left: '35%'}, {top: '20%', left: '85%'}],
    };

    // Stronger and more distinct illumination colors per section
    const illuminationColors = {
        asistencia: 'rgba(0, 255, 255, 0.7)', // darker teal
        platos_servidos: 'rgba(0, 255, 17, 0.7)', // darker red
        menu: 'rgba(0, 30, 255, 0.7)', // deeper blue
        gramaje: 'rgba(255, 217, 0, 0.7)', // richer yellow
        inventario: 'rgba(255, 0, 0, 0.7)', // deeper red
        reportes: 'rgba(89, 0, 255, 0.7)', // richer purple
        usuarios: 'rgba(0, 255, 170, 0.7)', // stronger green
    };

    function updateCirclesPosition(section) {
        const circles = backgroundCirclesContainer.querySelectorAll('.circle');
        const positions = circlePositions[section];
        if (!positions) return;
        circles.forEach((circle, index) => {
            circle.style.top = positions[index].top;
            circle.style.left = positions[index].left;
            // Increase circle size for stronger visual impact
            if (index === 0) {
                circle.style.width = '300px';
                circle.style.height = '300px';
            } else {
                circle.style.width = '180px';
                circle.style.height = '180px';
            }
        });
    }

    function updateIlluminationColor(section) {
        const color = illuminationColors[section] || 'transparent';
        illuminationOverlay.style.backgroundColor = color;
    }

    function updateDots() {
        if (!dotsContainer) return;
        const dots = dotsContainer.querySelectorAll('.slider-dot');
        dots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function createDots() {
        if (!dotsContainer) return;
        dotsContainer.innerHTML = '';
        slides.forEach((slide, index) => {
            const dot = document.createElement('div');
            dot.classList.add('slider-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                clearInterval(autoSlideInterval);
                showSlide(index);
                startAutoSlide();
            });
            dotsContainer.appendChild(dot);
        });
    }

    function showSlide(index) {
        if (isAnimating || index === currentIndex) return;
        isAnimating = true;

        const currentSlide = slides[currentIndex];
        const nextSlide = slides[index];

        // Animate current slide out
        currentSlide.style.transform = 'translateX(-100%)';
        currentSlide.style.opacity = '0';

        // Prepare next slide
        nextSlide.style.transform = 'translateX(100%)';
        nextSlide.style.opacity = '0';
        nextSlide.classList.add('active');

        // Animate next slide in
        setTimeout(() => {
            nextSlide.style.transform = 'translateX(0)';
            nextSlide.style.opacity = '1';
        }, 20);

        // After animation ends
        setTimeout(() => {
            currentSlide.classList.remove('active');
            currentSlide.style.transform = '';
            currentSlide.style.opacity = '';
            isAnimating = false;
            currentIndex = index;

            // Update circles and illumination
            const section = nextSlide.dataset.section;
            updateCirclesPosition(section);
            updateIlluminationColor(section);
            updateDots();

            // Update slider character image
            if (sliderCharacterImg) {
                const imageMap = {
                    asistencia: '../assets/imagenes/inicio/Asistencia.png',
                    platos_servidos: '../assets/imagenes/inicio/Platos.png',
                    menu: '../assets/imagenes/inicio/Menus.png',
                    gramaje: '../assets/imagenes/inicio/Gramaje.jpg',
                    inventario: '../assets/imagenes/inicio/Inventario.png',
                    reportes: '../assets/imagenes/inicio/Reportes.png',
                    usuarios: '../assets/imagenes/inicio/Usuarios.png',
                };
                sliderCharacterImg.src = imageMap[section] || sliderCharacterImg.src;
            }
        }, 520);
    }

    function showNext() {
        const nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
    }

    function showPrev() {
        const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            showNext();
        }, 4000);
    }

    if (slides.length > 0) {
        slides.forEach((slide, i) => {
            if (i === 0) {
                slide.classList.add('active');
                slide.style.transform = 'translateX(0)';
                slide.style.opacity = '1';
                // Initialize circles and illumination
                updateCirclesPosition(slide.dataset.section);
                updateIlluminationColor(slide.dataset.section);
            } else {
                slide.style.transform = 'translateX(100%)';
                slide.style.opacity = '0';
            }
        });
        createDots();
        startAutoSlide();
        nextBtn.addEventListener('click', () => {
            clearInterval(autoSlideInterval);
            showNext();
            startAutoSlide();
        });
        prevBtn.addEventListener('click', () => {
            clearInterval(autoSlideInterval);
            showPrev();
            startAutoSlide();
        });
    }

    // Mousemove event for dynamic illumination effect
    const slider = document.querySelector('.slider');
    if (slider) {
        slider.addEventListener('mousemove', (e) => {
            const rect = e.currentTarget.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            illuminationOverlay.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.3), transparent 80%)`;
        });

        slider.addEventListener('mouseleave', () => {
            const section = slides[currentIndex].dataset.section;
            updateIlluminationColor(section);
        });
    }

    // Button click handlers to redirect
    btnLinks.forEach(btn => {
        btn.addEventListener('click', () => {
            const link = btn.dataset.link;
            if (link) {
                window.location.href = link;
            }
        });
    });
});
