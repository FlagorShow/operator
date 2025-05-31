document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('card_number');
    const expiryDateInput = document.getElementById('expiry_date');
    const cvcInput = document.getElementById('cvc');
    const submitBtn = document.getElementById('submit-btn');

    // Форматирование номера карты
    cardNumberInput.addEventListener('input', function() {
        formatCardNumber(this);
        validateCard();
    });

    // Форматирование срока действия
    expiryDateInput.addEventListener('input', function() {
        formatExpiryDate(this);
        validateCard();
    });

    // Валидация CVC
    cvcInput.addEventListener('input', validateCard);

    function formatCardNumber(input) {
        let value = input.value.replace(/\s+/g, '');
        if (value.length > 0) {
            value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
        }
        input.value = value;
    }

    function formatExpiryDate(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        input.value = value;
    }

    function validateCard() {
        const cardNumber = cardNumberInput.value.replace(/\s+/g, '');
        const expiryDate = expiryDateInput.value;
        const cvc = cvcInput.value;
        
        // Проверка номера карты
        const isCardValid = cardNumber.length === 16 && luhnCheck(cardNumber);
        
        // Проверка срока действия
        const isExpiryValid = /^\d{2}\/\d{2}$/.test(expiryDate);
        
        // Проверка CVC
        const isCvcValid = /^\d{3}$/.test(cvc);
        
        // Активация кнопки
        submitBtn.disabled = !(isCardValid && isExpiryValid && isCvcValid);
        
        // Подсветка ошибок
        cardNumberInput.classList.toggle('error', !isCardValid && cardNumber.length > 0);
        expiryDateInput.classList.toggle('error', !isExpiryValid && expiryDate.length > 0);
        cvcInput.classList.toggle('error', !isCvcValid && cvc.length > 0);
    }

    function luhnCheck(cardNumber) {
        let sum = 0;
        let shouldDouble = false;
        
        for (let i = cardNumber.length - 1; i >= 0; i--) {
            let digit = parseInt(cardNumber.charAt(i));
            
            if (shouldDouble) {
                digit *= 2;
                if (digit > 9) {
                    digit -= 9;
                }
            }
            
            sum += digit;
            shouldDouble = !shouldDouble;
        }
        
        return (sum % 10) === 0;
    }
});