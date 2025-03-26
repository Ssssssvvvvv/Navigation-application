<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Получение данных текущего пользователя
$user_id = $_SESSION['id'];
$current_username = '';
$message = '';
$error = '';

try {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_username = $user['username'];
} catch (PDOException $e) {
    $error = "Ошибка при получении данных пользователя: " . $e->getMessage();
}

// Обработка формы изменения логина
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_username'])) {
    $new_username = trim($_POST['new_username']);
    
    if (empty($new_username)) {
        $error = "Введите новый логин";
    } else {
        try {
            // Проверка на уникальность логина
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error = "Этот логин уже занят";
            } else {
                // Обновление логина
                $stmt = $conn->prepare("UPDATE users SET username = :username WHERE id = :id");
                $stmt->bindParam(':username', $new_username);
                $stmt->bindParam(':id', $user_id);
                $stmt->execute();
                
                $_SESSION['username'] = $new_username;
                $current_username = $new_username;
                $message = "Логин успешно изменен";
            }
        } catch (PDOException $e) {
            $error = "Ошибка при изменении логина: " . $e->getMessage();
        }
    }
}

// Обработка формы изменения пароля
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Заполните все поля";
    } elseif ($new_password !== $confirm_password) {
        $error = "Новые пароли не совпадают";
    } else {
        try {
            // Проверка текущего пароля
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($current_password, $user['password'])) {
                // Обновление пароля
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':id', $user_id);
                $stmt->execute();
                
                $message = "Пароль успешно изменен";
            } else {
                $error = "Неверный текущий пароль";
            }
        } catch (PDOException $e) {
            $error = "Ошибка при изменении пароля: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #faeedd;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .section:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Профиль пользователя</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="section">
            <h2>Изменение логина</h2>
            <form method="post" action="profile.php">
                <div class="form-group">
                    <label for="current_username">Текущий логин:</label>
                    <input type="text" id="current_username" value="<?php echo htmlspecialchars($current_username); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="new_username">Новый логин:</label>
                    <input type="text" id="new_username" name="new_username" required>
                </div>
                <button type="submit" name="change_username">Изменить логин</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Изменение пароля</h2>
            <form method="post" action="profile.php">
                <div class="form-group">
                    <label for="current_password">Текущий пароль:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Новый пароль:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Подтвердите новый пароль:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password">Изменить пароль</button>
            </form>
        </div>
    </div>
</body>
</html>