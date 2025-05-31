<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['position_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("127.0.0.1", "root", "", "operator");
if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

// Получаем заявки с новыми данными
$stmt = $conn->prepare("SELECT id, full_name, last_submission_time, new_full_name, document_path 
                        FROM user 
                        WHERE last_submission_time IS NOT NULL AND documents_verified = 'Нет'");
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявки на обновление данных</title>
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
        .requests-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            flex: 1;
        }
        .request-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .request-info {
            margin-bottom: 15px;
        }
        .document-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .name-change {
            color: #ffcc00;
            font-weight: bold;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="requests-container">
        <h2 class="mb-4">Заявки на обновление данных клиентов</h2>
        
        <?php if (empty($requests)): ?>
            <div class="alert alert-info">Нет активных заявок</div>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <div class="request-card">
                    <div class="request-info">
                        <h5>Дата заявки: <?= htmlspecialchars($request['last_submission_time']) ?></h5>
                        
                        <div class="mb-2">
                            <span class="me-2">Текущее ФИО:</span>
                            <span><?= htmlspecialchars($request['full_name']) ?></span>
                        </div>
                        
                        <?php if ($request['new_full_name']): ?>
                            <div class="mb-2">
                                <span class="me-2">Новое ФИО:</span>
                                <span class="name-change"><?= htmlspecialchars($request['new_full_name']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($request['document_path']): ?>
                            <div class="mb-2">
                                <span>Документ:</span>
                                <a href="<?= htmlspecialchars($request['document_path']) ?>" target="_blank" class="d-block">
                                    <img src="<?= htmlspecialchars($request['document_path']) ?>" alt="Документ" class="document-preview">
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="mb-2">Документ не загружен</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="btn-group">
                        <a href="approve_request.php?id=<?= $request['id'] ?>&new_name=<?= urlencode($request['new_full_name']) ?>" 
                           class="btn btn-success">Подтвердить</a>
                        <a href="reject_request.php?id=<?= $request['id'] ?>" class="btn btn-danger">Отклонить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Назад</a>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>