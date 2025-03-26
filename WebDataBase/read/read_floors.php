<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM floors");
    $stmt->execute();
    $floors = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Список этажей</title>
    <link rel="stylesheet" type="text/css" href="../style_table.css">

</head>
<body>
<h1>Список этажей</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Корпус</th>
        <th colspan="2">Действия</th>
    </tr>
    <?php foreach ($floors as $floor) { ?>
        <tr>
            <td><?php echo $floor['floor_id']; ?></td>
            <td><?php echo $floor['floor_number']; ?></td>
            <td><?php echo $floor['building_id']; ?></td>
            <td>
                <?php echo '<a href="../update/update_floor.php?id=' . $floor['floor_id'] . '">Редактировать</a>'; ?>
            </td>
            <td>
                <?php echo '<a href="../update/update_floor.php?id=' . $floor['floor_id'] . '" onclick="return confirm(\'Вы уверены?\');">Удалить</a>';?>
            </td>
        </tr>
    <?php } ?>
</table>
</body>
</html>