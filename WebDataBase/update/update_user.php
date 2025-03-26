<?php
session_start();
require_once '../config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Обработка GET-запроса для получения данных пользователя
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) {
        $user_id = $_GET['id'];
        
        try {
            // Получение данных пользователя по ID
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if (!$user) {
                header("Location: ../read/read_users.php");
                exit;
            }
        } catch (PDOException $e) {
            echo "Ошибка при получении данных пользователя: " . $e->getMessage();
            exit;
        }
    }
}

// Обработка POST-запроса для обновления данных пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['role']) && !empty($_POST['id'])) {
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        $user_id = $_POST['id'];
        $password = !empty($_POST['password']) ? trim($_POST['password']) : null;

        try {
            // Проверка, существует ли пользователь с таким именем (кроме текущего)
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Пользователь с таким именем уже существует.";
            } else {
                // Обновление данных пользователя
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username = :username, role = :role, password = :password WHERE id = :id");
                    $stmt->bindParam(':password', $hashed_password);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
                }
                
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':id', $user_id);

                if ($stmt->execute()) {
                    $message = "Пользователь успешно обновлен.";
                } else {
                    $error = "Ошибка при обновлении пользователя.";
                }
            }
        } catch (PDOException $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    } else {
        $error = "Пожалуйста, заполните все обязательные поля.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Обновление пользователя</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">
</head>
<body>
<h1>Обновление пользователя</h1>

<?php if (!empty($message)): ?>
    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <!-- Скрытое поле для передачи ID пользователя -->
    <input type="hidden" name="id" value="<?php echo $user_id; ?>">

    <label for="username">Логин:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

    <label for="password">Новый пароль (оставьте пустым, чтобы не менять):</label><br>
    <input type="password" name="password"><br><br>

    <label for="role">Роль:</label><br>
    <select name="role" required>
        <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>Пользователь</option>
        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Администратор</option>
    </select><br><br>

    <button type="submit">Обновить пользователя</button>
</form>

</body>
</html>