<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
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
    <meta charset="UTF-8">
    <title>Список зданий</title>
    <link rel="stylesheet" type="text/css" href="../style_table.css">

</head>
<body>
<h1>Список зданий</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th colspan="2">Действия</th>
    </tr>
    <?php foreach ($buildings as $building) { ?>
        <tr>
            <td><?php echo $building['building_id']; ?></td>
            <td><?php echo $building['building_name']; ?></td>
            <td> <?php echo '<a href="../update/update_building.php?id=' . $building['building_id'] . '">Редактировать</a>'; ?></td>
            <td> <?php echo '<a href="../delete/delete_building.php?id=' . $building['building_id'] . '" onclick="return confirm(\'Вы уверены?\');">Удалить</a>'; ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>