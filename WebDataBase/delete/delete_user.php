<?php
session_start();
require_once '../config.php';

// Проверка прав администратора
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';
$error = '';

// Обработка удаления пользователя
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Нельзя удалить самого себя
    if ($user_id == $_SESSION['id']) {
        $error = "Вы не можете удалить самого себя";
    } else {
        try {
            // Проверка существования пользователя
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                $error = "Пользователь не найден";
            } else {
                // Удаление пользователя
                $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id);
                $stmt->execute();
                
                $message = "Пользователь успешно удален";
            }
        } catch (PDOException $e) {
            $error = "Ошибка при удалении пользователя: " . $e->getMessage();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id'])) {
    $error = "Не указан ID пользователя";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление пользователя</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
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
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 5px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .user-list {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Удаление пользователя</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="user-list">
            <h2>Список пользователей</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Роль</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        // Получение списка пользователей (кроме текущего администратора)
                        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id != :current_id");
                        $stmt->bindParam(':current_id', $_SESSION['id']);
                        $stmt->execute();
                        
                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                            echo "<td><a href='delete_user.php?id=" . $user['id'] . "' class='btn btn-danger' onclick='return confirm(\"Вы уверены, что хотите удалить этого пользователя?\");'>Удалить</a></td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='4'>Ошибка при загрузке пользователей: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="actions">
            <a href="../index.php" class="btn">Вернуться на главную</a>
        </div>
    </div>
</body>
</html>