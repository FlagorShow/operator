<?php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

// Проверяем, что пользователь бизнес-клиент
if ($_SESSION['user']['position_id'] != 4) {
    header("Location: tariffs.php");
    exit();
}

require_once '../config/config.php';
$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$user_id = $_SESSION['user']['id'];
$sql = "SELECT user.*, tariff.data_gb, tariff.minutes, tariff.sms 
        FROM user 
        JOIN tariff ON user.tariff_id = tariff.id 
        WHERE user.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Данные пользователя не найдены.");
}

$hour = date('H');
if ($hour >= 5 && $hour < 12) $greeting = "Доброе утро";
elseif ($hour >= 12 && $hour < 18) $greeting = "Добрый день";
elseif ($hour >= 18 && $hour < 23) $greeting = "Добрый вечер";
else $greeting = "Доброй ночи";

include '../templates/header.php';

if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Бизнес-тарифы</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/tariffs.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
        }
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-confirm {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-confirm:hover {
            background-color: #0069d9;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="tariff-title">Бизнес-тарифы</h1>
        <p class="tariff-subtitle">Специальные предложения для бизнеса!</p>

        <div class="carousel">
            <div class="carousel-inner">
                <?php
                $conn = new mysqli("127.0.0.1", "root", "", "operator");
                if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

                $sql = "SELECT * FROM tariff WHERE id IN (11, 22, 33, 44)";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="tariff-block">';
                        echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
                        echo '<p class="price">' . htmlspecialchars($row['monthly_cost']) . ' ₽</p>';
                        echo '<p class="details">' . htmlspecialchars($row['minutes']) . ' минут, ' . htmlspecialchars($row['data_gb']) . ' ГБ, ' . htmlspecialchars($row['sms']) . ' СМС</p>';
                        echo '<p class="services">' . htmlspecialchars($row['additional_services']) . '</p>';
                        echo '<button class="btn btn-primary connect-btn" data-tariff-id="' . $row['id'] . '">Оформить</button>';
                        echo '</div>';
                    }
                } else {
                    echo "Тарифы не найдены.";
                }
                $conn->close();
                ?>
            </div>
            <button class="carousel-control-prev" onclick="prevSlide()">&#10094;</button>
            <button class="carousel-control-next" onclick="nextSlide()">&#10095;</button>
        </div>

        <div style="text-align: center; margin: 40px 0;">
            <div style="display: inline-flex; gap: 10px;">
                <a href="index.php" class="back-button">Домой</a>
            </div>
        </div>
    </div>

    <!-- Модальное окно для подтверждения -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Вы уверены, что хотите сменить тариф?</p>
            <div class="modal-buttons">
                <button id="confirmYes" class="btn-confirm">Да</button>
                <button id="confirmNo" class="btn-cancel">Нет</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmationModal');
            let selectedTariffId = null;

            // Обработчики кнопок "Оформить"
            document.querySelectorAll('.connect-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectedTariffId = this.getAttribute('data-tariff-id');
                    modal.style.display = 'block';
                });
            });

            // Обработчики модального окна
            document.getElementById('confirmYes').addEventListener('click', function() {
                if (selectedTariffId) {
                    window.location.href = `change_tariff.php?tariff_id=${selectedTariffId}`;
                }
            });

            document.getElementById('confirmNo').addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Закрытие при клике вне окна
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Код карусели
            let currentSlide = 0;
            const inner = document.querySelector('.carousel-inner');
            const blocks = document.querySelectorAll('.tariff-block');
            const totalSlides = blocks.length;

            function updateCarousel() {
                inner.style.transform = `translateX(-${currentSlide * 33.33}%)`;
                document.querySelector('.carousel-control-prev').style.display = currentSlide === 0 ? 'none' : 'block';
                document.querySelector('.carousel-control-next').style.display = currentSlide >= totalSlides - 3 ? 'none' : 'block';
            }

            window.nextSlide = function() {
                if (currentSlide < totalSlides - 3) {
                    currentSlide++;
                    updateCarousel();
                }
            };

            window.prevSlide = function() {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateCarousel();
                }
            };

            updateCarousel();
        });
    </script>
</body>
</html>