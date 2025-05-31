// services_carousel.js - новый файл для карусели услуг
let currentSlide = 0;
const inner = document.querySelector('.packages-grid');
const blocks = document.querySelectorAll('.package-block');
const totalSlides = blocks.length;
const visibleSlides = 3; // Количество видимых блоков одновременно

// Элементы модального окна
const modal = document.getElementById('confirmationModal');
const confirmYes = document.getElementById('confirmYes');
const confirmNo = document.getElementById('confirmNo');
let activeForm = null;

// Инициализация карусели
function initCarousel() {
    // Преобразуем grid в flex для карусели
    inner.style.display = 'flex';
    inner.style.transition = 'transform 0.5s ease';
    inner.style.gap = '20px';
    inner.style.flexWrap = 'nowrap';
    inner.style.overflow = 'hidden';
    
    updateButtons();
}

// Обновление состояния кнопок навигации
function updateButtons() {
    const prevBtn = document.querySelector('.carousel-control-prev');
    const nextBtn = document.querySelector('.carousel-control-next');
    
    if (prevBtn && nextBtn) {
        prevBtn.style.display = currentSlide === 0 ? 'none' : 'block';
        nextBtn.style.display = currentSlide >= totalSlides - visibleSlides ? 'none' : 'block';
    }
}

// Переход к следующему слайду
function nextSlide() {
    if (currentSlide < totalSlides - visibleSlides) {
        currentSlide++;
        updateCarousel();
    }
}

// Переход к предыдущему слайду
function prevSlide() {
    if (currentSlide > 0) {
        currentSlide--;
        updateCarousel();
    }
}

// Обновление позиции карусели
function updateCarousel() {
    const blockWidth = blocks[0].offsetWidth + 20; // Ширина блока + gap
    inner.style.transform = `translateX(-${currentSlide * blockWidth}px)`;
    updateButtons();
}

// Обработчики для модального окна
document.querySelectorAll('.package-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        activeForm = this;
        modal.style.display = 'flex';
    });
});

confirmYes.addEventListener('click', function() {
    if (activeForm) {
        activeForm.submit();
    }
    modal.style.display = 'none';
});

confirmNo.addEventListener('click', function() {
    modal.style.display = 'none';
    activeForm = null;
});

window.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.style.display = 'none';
        activeForm = null;
    }
});

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    initCarousel();
    
    // Проверяем наличие ошибки
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert && errorAlert.textContent.includes('не найдено')) {
        modal.style.display = 'none';
    }
});