<?php

session_start();

if (!isset($_SESSION['user']['id'])) {

    header("Location: ../login.php");

    exit();

}



require_once '../config/config.php';

$conn = new mysqli("127.0.0.1", "root", "", "operator");

if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);



ini_set('upload_max_filesize', '10M');

ini_set('post_max_size', '10M');

ini_set('max_execution_time', 300);



$user_id = $_SESSION['user']['id'];

$error = '';

$success = '';

$show_modal = false;

$time_left = null;



// Получаем время последней отправки

$stmt = $conn->prepare("SELECT full_name, last_submission_time FROM user WHERE id = ?");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$current_user = $result->fetch_assoc();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Проверка временного ограничения

    if ($current_user['last_submission_time']) {

        $last_submission = new DateTime($current_user['last_submission_time']);

        $now = new DateTime();

        $interval = $last_submission->diff($now);

        

        if ($interval->h < 24 && $interval->days == 0) {

            $hours_left = 24 - $interval->h - 1;

            $minutes_left = 60 - $interval->i;

            $time_left = "Отправить данные можно через $hours_left ч. $minutes_left мин.";

            $error = 'Вы можете отправлять данные на проверку только раз в 24 часа';

        }

    }



    if (empty($error)) {

        if (!isset($_POST['full_name']) || empty($_POST['full_name'])) {

            $error = 'Поле "ФИО" обязательно для заполнения!';

        } else {

            $new_full_name = trim($_POST['full_name']);

            $new_full_name = preg_replace('/\s+/u', ' ', $new_full_name);

            

            // Проверка ФИО

            $name_parts = explode(' ', $new_full_name);

            if (count($name_parts) < 3) {

                $error = 'ФИО должно содержать минимум 3 слова (Фамилия Имя Отчество)';

            } else {

                foreach ($name_parts as $part) {

                    if (!preg_match('/^\p{Lu}/u', $part)) {

                        $error = 'Каждое слово в ФИО должно начинаться с заглавной буквы';

                        break;

                    }

                }

            }

            

            if (empty($error)) {

                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

                    $upload_dir = '../uploads/';

                    if (!file_exists($upload_dir)) {

                        mkdir($upload_dir, 0777, true);

                    }

                    

                    if ($_FILES['file']['size'] > 10 * 1024 * 1024) {

                        $error = 'Размер файла не должен превышать 10MB';

                    } else {

                        $file_name = basename($_FILES['file']['name']);

                        $target_path = $upload_dir . $file_name;

                        

                        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {

                            // Обновляем время последней отправки

                            $update_stmt = $conn->prepare("UPDATE user SET last_submission_time = NOW() WHERE id = ?");

                            $update_stmt->bind_param("i", $user_id);

                            $update_stmt->execute();

                            

                            $show_modal = true;

                            $success = 'Файл успешно загружен и отправлен на проверку.';

                        } else {

                            $error = 'Ошибка загрузки файла.';

                        }

                    }

                }

                

                if (empty($error) && $new_full_name !== $current_user['full_name']) {

                    $success = ($success ? $success . ' ' : '') . 

                              'Изменение ФИО будет применено после проверки модератором.';

                }

            }

        }

    }

}



// Получаем обновленное время последней отправки

$stmt->execute();

$result = $stmt->get_result();

$current_user = $result->fetch_assoc();

?>



<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <title>Настройки</title>

    <link rel="icon" type="image/jpeg" href="../images/logo.png" sizes="16x16">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../styles/styles.css">

    <style>

        body {

            color: white;

            background-image: url('../images/background.jpg');

            background-size: cover;

        }

        .file-info {

            margin-top: 5px;

            font-size: 0.9em;

            color: red;

        }

        .form-label {

            color: white;

        }

        .text-muted {

            color: white !important;

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

            color: black;

        }

        .close-modal {

            margin-top: 15px;

            padding: 8px 20px;

            background-color: #007bff;

            color: white;

            border: none;

            border-radius: 4px;

            cursor: pointer;

        }

    </style>

</head>

<body>

    <?php include '../templates/header.php'; ?>

    

    <div class="container mt-5">

        <h2 style="color: white;">Настройки профиля</h2>

        

        <?php if ($error): ?>

            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>

        <?php endif; ?>

        

        <?php if ($success): ?>

            <div class="alert alert-info"><?= htmlspecialchars($success) ?></div>

        <?php endif; ?>



        <form action="settings.php" method="post" enctype="multipart/form-data">

            <div class="mb-3">

                <label for="full_name" class="form-label">ФИО</label>

                <input type="text" class="form-control" id="full_name" name="full_name" 

                       value="<?= htmlspecialchars($current_user['full_name'] ?? '') ?>" required>

                <small class="text-muted">Введите ФИО полностью (минимум 3 слова), каждое слово с заглавной буквы</small>

            </div>

            

            <div class="mb-3">

                <label for="file" class="form-label">Загрузить документ</label>

                <input type="file" class="form-control" id="file" name="file">

                <div class="file-info">Максимальный размер файла: 10MB</div>

                <small class="text-muted">Документы будут проверены модератором в течение 24 часов</small>

            </div>

            

            <button type="submit" class="btn btn-primary">Отправить на проверку</button>

            <a href="settings.php" class="btn btn-secondary">Назад</a>

        </form>

    </div>



    <div id="verificationModal" class="modal" style="<?= $show_modal ? 'display: block;' : 'display: none;' ?>">

        <div class="modal-content">

            <h3>Документы отправлены на проверку</h3>

            <p>Ваши документы были успешно загружены и отправлены на ручную проверку.</p>

            <p>Мы уведомим вас о результате проверки.</p>

            <button class="close-modal" onclick="document.getElementById('verificationModal').style.display='none'">OK</button>

        </div>

    </div>



    <?php include '../templates/footer.php'; ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        <?php if ($show_modal): ?>

        setTimeout(function() {

            document.getElementById('verificationModal').style.display = 'none';

        }, 5000);

        <?php endif; ?>

    </script>

</body>

</html>



<?php

$conn->close();

?>