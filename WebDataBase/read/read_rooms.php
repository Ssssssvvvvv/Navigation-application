<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

try {
    // Запрос на получение всех комнат
    $stmt = $conn->prepare("SELECT * FROM rooms");
    $stmt->execute();
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Список комнат</title>
    <link rel="stylesheet" type="text/css" href="../style_table.css">
</head>
<body>
<h1>Список комнат</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Номер аудитории</th>
        <th>Тип комнаты</th>
        <th>ID этажа</th>
        <th>ID корпуса</th>
        <th>Информация</th>
        <th>Позиция X</th>
        <th>Позиция Y</th>
        <th colspan="2">Действия</th>
    </tr>
    <?php foreach ($rooms as $room) { ?>
        <tr>
            <td><?php echo $room['room_id']; ?></td>
            <td><?php echo $room['room_number']; ?></td>
            <td><?php echo $room['room_type']; ?></td>
            <td><?php echo $room['floor_id']; ?></td>
            <td><?php echo $room['building_id']; ?></td>
            <td><?php echo $room['info']; ?></td>
            <td><?php echo $room['position_x']; ?></td>
            <td><?php echo $room['position_y']; ?></td>
            <td><?php echo '<a href="../update/update_room.php?id=' . $room['room_id'] . '">Редактировать</a>';?></td>
            <td><?php echo '<a href="../delete/delete_room.php?id=' . $room['room_id'] . '" onclick="return confirm(\'Вы уверены?\');">Удалить</a>'; ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>