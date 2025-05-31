let currentSlide = 0;
const inner = document.querySelector('.carousel-inner');
const blocks = document.querySelectorAll('.tariff-block');
const totalSlides = blocks.length;

// Элементы модального окна
const modal = document.getElementById('confirmationModal');
const confirmYes = document.getElementById('confirmYes');
const confirmNo = document.getElementById('confirmNo');

// Переменная для хранения выбранного тарифа
let selectedTariffId = null;

// Обработчик для кнопки "Оформить"
document.querySelectorAll('.btn-primary').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Проверка времени последней смены
        const lastChange = localStorage.getItem('lastTariffChange');
        if (lastChange && Date.now() - lastChange < 60000) {
            alert('Сменить тариф можно не чаще чем раз в минуту.');
            return;
        }

        // Получаем ID тарифа из href
        selectedTariffId = new URL(button.href).searchParams.get('tariff_id');
        
        // Показываем модальное окно
        modal.style.display = 'flex'; // Тут активируем окно
    });
});

// Обработчик для кнопки "Да" в модальном окне
confirmYes.addEventListener('click', () => {
    // Сохраняем время смены тарифа
    localStorage.setItem('lastTariffChange', Date.now());

    // Перенаправляем пользователя на страницу смены тарифа
    window.location.href = `change_tariff.php?tariff_id=${selectedTariffId}`;
});

// Обработчик для кнопки "Нет" в модальном окне
confirmNo.addEventListener('click', () => {
    // Скрываем модальное окно
    modal.style.display = 'none';
});

// Закрытие модального окна при клике вне его
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Остальной код карусели
function updateButtons() {
    const prevBtn = document.querySelector('.carousel-control-prev');
    const nextBtn = document.querySelector('.carousel-control-next');
    prevBtn.style.display = currentSlide === 0 ? 'none' : 'block';
    nextBtn.style.display = currentSlide >= totalSlides - 3 ? 'none' : 'block';
}

function nextSlide() {
    if (currentSlide < totalSlides - 3) {
        currentSlide++;
        inner.style.transform = `translateX(-${currentSlide * 33.33}%)`;
        updateButtons();
    }
}

function prevSlide() {
    if (currentSlide > 0) {
        currentSlide--;
        inner.style.transform = `translateX(-${currentSlide * 33.33}%)`;
        updateButtons();
    }
}

// Добавьте этот код в файл tariffs.js
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем наличие ошибки "Тариф не существует."
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert && errorAlert.textContent.includes('Тариф не существует.')) {
        const modal = document.getElementById('confirmationModal');
        if (modal) {
            modal.style.display = 'none'; // Скрываем модальное окно
        }
    }
});
// Инициализация кнопок при загрузке
document.addEventListener('DOMContentLoaded', updateButtons);