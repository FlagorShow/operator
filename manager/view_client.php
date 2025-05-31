<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: client_requests.php");
    exit();
}

$client_id = $_GET['id'];
$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT u.*, t.name as tariff_name FROM user u LEFT JOIN tariff t ON u.tariff_id = t.id WHERE u.id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    header("Location: client_requests.php");
    exit();
}

$tariffs_stmt = $conn->prepare("SELECT id, name FROM tariff WHERE id IN (11,22,33,44,55,555,666,777)");
$tariffs_stmt->execute();
$tariffs = $tariffs_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Данные клиента</title>
    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white;
            background-image: url('../images/background.jpg');
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .client-container {
            flex: 1;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
        }
        .client-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="client-container">
        <h2 class="mb-4">Информация о клиенте</h2>
        
        <div class="client-info">
            <div class="info-row">
                <span class="info-label">ФИО:</span>
                <span><?= htmlspecialchars($client['full_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Телефон:</span>
                <span><?= htmlspecialchars($client['phone_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span><?= htmlspecialchars($client['email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Баланс:</span>
                <span><?= htmlspecialchars($client['balance']) ?> руб.</span>
            </div>
            <div class="info-row">
                <span class="info-label">Текущий тариф:</span>
                <span><?= htmlspecialchars($client['tariff_name']) ?></span>
                <button class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#changeTariffModal">Сменить</button>
            </div>
            <div class="info-row">
                <span class="info-label">Статус верификации:</span>
                <span><?= htmlspecialchars($client['documents_verified']) ?></span>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="clients.php" class="btn btn-secondary">Назад к клиентам</a>
            <a href="recharge_balance.php?client_id=<?= $client_id ?>" class="btn btn-warning">Пополнить баланс</a>
            <a href="change_client_password.php?client_id=<?= $client_id ?>" class="btn btn-danger">Сменить пароль</a>
        </div>
    </div>

    <!-- Modal для смены тарифа -->
    <div class="modal fade" id="changeTariffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Смена тарифа</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changeTariffForm" action="change_client_tariff.php" method="post">
                        <input type="hidden" name="client_id" value="<?= $client_id ?>">
                        <div class="mb-3">
                            <label class="form-label">Выберите новый тариф</label>
                            <select class="form-select" name="new_tariff_id" required>
                                <?php foreach ($tariffs as $tariff): ?>
                                    <option value="<?= $tariff['id'] ?>" <?= $tariff['id'] == $client['tariff_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tariff['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" form="changeTariffForm" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>