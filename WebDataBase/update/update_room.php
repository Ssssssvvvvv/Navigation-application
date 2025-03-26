<?php
session_start();
require_once '../config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Обработка GET-запроса для получения данных комнаты
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) {
        $room_id = $_GET['id'];
        
        try {
            // Получение данных комнаты по ID
            $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = :room_id");
            $stmt->bindParam(':room_id', $room_id);
            $stmt->execute();
            $room = $stmt->fetch();
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        // Если ID не указан, перенаправляем на страницу списка комнат
        header("Location: ../read/read_rooms.php");
        exit;
    }
}

// Обработка POST-запроса для обновления данных комнаты
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['room_type']) && !empty($_POST['room_number']) && !empty($_POST['floor_id']) && !empty($_POST['building_id']) && !empty($_POST['info']) && !empty($_POST['position_x']) && !empty($_POST['position_y']) && !empty($_POST['room_id'])) {
        $room_type = $_POST['room_type'];
        $room_number = $_POST['room_number'];
        $floor_id = $_POST['floor_id'];
        $building_id = $_POST['building_id'];
        $info = $_POST['info'];
        $position_x = $_POST['position_x'];
        $position_y = $_POST['position_y'];
        $room_id = $_POST['room_id'];

        // Проверка существования floor_id и building_id в базе данных
        $stmt = $conn->prepare("SELECT floor_id FROM floors WHERE floor_id = :floor_id");
        $stmt->bindParam(':floor_id', $floor_id);
        $stmt->execute();

        $stmt2 = $conn->prepare("SELECT building_id FROM buildings WHERE building_id = :building_id");
        $stmt2->bindParam(':building_id', $building_id);
        $stmt2->execute();

        if ($stmt->rowCount() > 0 && $stmt2->rowCount() > 0) {
            try {
                // Обновление данных комнаты
                $stmt = $conn->prepare("UPDATE rooms SET room_type = :room_type, room_number = :room_number, floor_id = :floor_id, building_id = :building_id, info = :info, position_x = :position_x, position_y = :position_y WHERE room_id = :room_id");
                $stmt->bindParam(':room_type', $room_type);
                $stmt->bindParam(':room_number', $room_number);
                $stmt->bindParam(':floor_id', $floor_id);
                $stmt->bindParam(':building_id', $building_id);
                $stmt->bindParam(':info', $info);
                $stmt->bindParam(':position_x', $position_x);
                $stmt->bindParam(':position_y', $position_y);
                $stmt->bindParam(':room_id', $room_id);

                if ($stmt->execute()) {
                    echo "Комната успешно обновлена.";
                } else {
                    echo "Ошибка при обновлении комнаты.";
                }
            } catch (PDOException $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        } else {
            echo "Этаж или корпус с указанным ID не существует.";
        }
    } else {
        echo "Пожалуйста, заполните все поля.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Обновление комнаты</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
<h1>Обновление комнаты</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <!-- Скрытое поле для передачи ID комнаты -->
    <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

    <label for="room_number">Номер аудитории:</label><br>
    <input type="number" name="room_number" value="<?php echo $room['room_number']; ?>" required><br><br>

    <label for="room_type">Тип комнаты:</label><br>
    <select name="room_type" required>
        <option value="лекционная" <?php echo ($room['room_type'] == 'лекционная') ? 'selected' : ''; ?>>Лекционная</option>
        <option value="компьютерная" <?php echo ($room['room_type'] == 'компьютерная') ? 'selected' : ''; ?>>Компьютерная</option>
        <option value="раздевалка" <?php echo ($room['room_type'] == 'раздевалка') ? 'selected' : ''; ?>>Раздевалка</option>
        <option value="деканат" <?php echo ($room['room_type'] == 'деканат') ? 'selected' : ''; ?>>Деканат</option>
        <option value="кафедра" <?php echo ($room['room_type'] == 'кафедра') ? 'selected' : ''; ?>>Кафедра</option>
    </select><br><br>

    <label for="floor_id">ID этажа:</label><br>
    <input type="number" name="floor_id" value="<?php echo $room['floor_id']; ?>" required><br><br>

    <label for="building_id">ID корпуса:</label><br>
    <input type="number" name="building_id" value="<?php echo $room['building_id']; ?>" required><br><br>

    <label for="info">Информация о комнате:</label><br>
    <textarea name="info" rows="4" cols="50" required><?php echo $room['info']; ?></textarea><br><br>

    <label for="position_x">Позиция X:</label><br>
    <input type="number" name="position_x" value="<?php echo $room['position_x']; ?>" required><br><br>

    <label for="position_y">Позиция Y:</label><br>
    <input type="number" name="position_y" value="<?php echo $room['position_y']; ?>" required><br><br>

    <button type="submit">Обновить комнату</button>
</form>

</body>
</html>