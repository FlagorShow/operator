<?php
session_start();
require_once '../config/config.php';

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit();
}

$packages = $conn->query("SELECT * FROM packages WHERE type = 'minutes'");

include '../templates/header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пакеты минут</title>
    <link rel="stylesheet" href="../styles/tariffs.css">
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <style>
        body {
            background-image: url('../images/background.jpg');
            background-size: cover;
        }
        .alert {
            max-width: 500px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
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
        .back-button {
            display: inline-block;
            background-color: #e53e3e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-family: Jost, sans-serif;
            font-size: 16px;
            min-width: 100px;
        }
        .back-button:hover {
            background-color: #c53030;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="tariff-title">Пакеты минут</h1>
        <p class="tariff-subtitle">Выберите подходящий пакет</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="carousel">
            <div class="carousel-inner">
                <?php while($package = $packages->fetch_assoc()): ?>
                    <div class="tariff-block">
                        <h2><?= htmlspecialchars($package['amount']) ?> минут</h2>
                        <p class="price"><?= htmlspecialchars($package['price']) ?> ₽</p>
                        <p class="details"><?= htmlspecialchars($package['description']) ?></p>
                        <button class="btn btn-primary connect-btn" data-package-id="<?= $package['id'] ?>">Подключить</button>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="carousel-control-prev" onclick="prevSlide()">&#10094;</button>
            <button class="carousel-control-next" onclick="nextSlide()">&#10095;</button>
        </div>

        <div style="text-align: center; margin: 40px 0;">
            <div style="display: inline-flex; gap: 10px;">
                <a href="services.php" class="back-button">Назад</a>
                <a href="index.php" class="back-button">Домой</a>
            </div>
        </div>

    <!-- Модальное окно подтверждения -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Вы уверены, что хотите подключить этот пакет?</p>
            <div class="modal-buttons">
                <button id="confirmYes" class="btn-confirm">Да</button>
                <button id="confirmNo" class="btn-cancel">Нет</button>
            </div>
        </div>
    </div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmationModal');
            let selectedPackageId = null;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'buy_package.php';
            form.style.display = 'none';
            document.body.appendChild(form);

            document.querySelectorAll('.connect-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectedPackageId = this.getAttribute('data-package-id');
                    modal.style.display = 'block';
                });
            });

            document.getElementById('confirmYes').addEventListener('click', function() {
                if (selectedPackageId) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'package_id';
                    input.value = selectedPackageId;
                    form.innerHTML = '';
                    form.appendChild(input);
                    form.submit();
                }
            });

            document.getElementById('confirmNo').addEventListener('click', function() {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

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
<?php $conn->close(); ?>