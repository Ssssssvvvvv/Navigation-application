<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Список пользователей</title>
    <link rel="stylesheet" type="text/css" href="../style_table.css">
</head>
<body>
<h1>Список пользователей</h1>
<a href="../create/create_user.php" class="button" target="content"><h4>Создать нового пользователя</h4></a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Логин</th>
        <th>Роль</th>
        <th colspan="2">Действия</th>
    </tr>
    <?php foreach ($users as $user) { ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['role']; ?></td>
            <td><?php echo '<a href="../update/update_user.php?id=' . $user['id'] . '">Редактировать</a>';?></td>
            <td><?php echo '<a href="../delete/delete_user.php?id=' . $user['id'] . '" onclick="return confirm(\'Вы уверены?\');">Удалить</a>';?></td>            
        </tr>
    <?php } ?>
</table>
</body>
</html>