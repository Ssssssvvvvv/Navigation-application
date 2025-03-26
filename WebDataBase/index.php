<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] === 'admin') {
    echo "<p>Вы вошли как администратор.</p>";
} else {
    echo "<p>Вы вошли как обычный пользователь.</p>";
}
try {
    $stmt = $conn->prepare("SELECT * FROM buildings");
    $stmt->execute();
    $buildings = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="style/style_table.css">

</head>
<body>

<div class="content">
    <div class="buildings">
        <h1>Корпусы</h1><br>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Название здания</th>
                <th colspan="2">Действия</th>
            </tr>
            <?php foreach ($buildings as $building) { ?>
                <tr>
                    <td><?php echo $building['building_id']; ?></td>
                    <td><?php echo $building['building_name']; ?></td>
                    <td><a href="update/update_building.php?id=<?php echo $building['building_id']; ?>">Редактировать</a></td>
                    <td><a href="delete/delete_building.php?id=<?php echo $building['building_id']; ?> onclick="return confirm('Вы уверены?');">Удалить</a></td>
                </tr>
            <?php } ?>
    </div>

</body>

