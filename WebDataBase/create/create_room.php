<?php
session_start();
require_once '../config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Инициализация переменных для сохранения данных формы
$room_number = $room_type = $floor_id = $building_id = $info = $position_x = $position_y = '';

// Обработка POST-запроса для создания комнаты
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверка наличия всех обязательных полей
    if (!empty($_POST['room_type']) && !empty($_POST['room_number']) && !empty($_POST['floor_id']) && !empty($_POST['building_id']) && !empty($_POST['info']) && !empty($_POST['position_x']) && !empty($_POST['position_y'])) {
        $room_type = $_POST['room_type'];
        $room_number = $_POST['room_number'];
        $floor_id = $_POST['floor_id'];
        $building_id = $_POST['building_id'];
        $info = $_POST['info'];
        $position_x = $_POST['position_x'];
        $position_y = $_POST['position_y'];

        // Проверка существования floor_id в базе данных
        $stmt = $conn->prepare("SELECT floor_id FROM floors WHERE floor_id = :floor_id");
        $stmt->bindParam(':floor_id', $floor_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            try {
                // Вставка данных в таблицу rooms
                $stmt = $conn->prepare("INSERT INTO rooms (room_type, room_number, floor_id, building_id, info, position_x, position_y) VALUES (:room_type, :room_number, :floor_id, :building_id, :info, :position_x, :position_y)");
                $stmt->bindParam(':room_type', $room_type);
                $stmt->bindParam(':room_number', $room_number);
                $stmt->bindParam(':floor_id', $floor_id);
                $stmt->bindParam(':building_id', $building_id);
                $stmt->bindParam(':info', $info);
                $stmt->bindParam(':position_x', $position_x);
                $stmt->bindParam(':position_y', $position_y);

                if ($stmt->execute()) {
                    echo "Комната успешно создана.";
                    // Очистка полей после успешного создания
                    $room_number = $room_type = $floor_id = $building_id = $info = $position_x = $position_y = '';
                } else {
                    echo "Ошибка при создании комнаты.";
                }
            } catch (PDOException $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        } else {
            echo "Этаж с указанным ID не существует.";
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
    <title>Создание комнаты</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
<h1>Создание комнаты</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <label for="room_number">Номер аудитории:</label><br>
    <input type="number" name="room_number" value="<?php echo htmlspecialchars($room_number); ?>" required><br><br>

    <label for="room_type">Тип комнаты:</label><br>
    <select name="room_type" required>
        <option value="лекционная" <?php echo ($room_type == 'лекционная') ? 'selected' : ''; ?>>Лекционная</option>
        <option value="компьютерная" <?php echo ($room_type == 'компьютерная') ? 'selected' : ''; ?>>Компьютерная</option>
        <option value="раздевалка" <?php echo ($room_type == 'раздевалка') ? 'selected' : ''; ?>>Раздевалка</option>
        <option value="деканат" <?php echo ($room_type == 'деканат') ? 'selected' : ''; ?>>Деканат</option>
        <option value="кафедра" <?php echo ($room_type == 'кафедра') ? 'selected' : ''; ?>>Кафедра</option>
    </select><br><br>

    <label for="floor_id">ID этажа:</label><br>
    <input type="number" name="floor_id" value="<?php echo htmlspecialchars($floor_id); ?>" required><br><br>

    <label for="building_id">ID корпуса:</label><br>
    <input type="number" name="building_id" value="<?php echo htmlspecialchars($building_id); ?>" required><br><br>

    <label for="info">Информация о комнате:</label><br>
    <textarea name="info" rows="4" cols="50" required><?php echo htmlspecialchars($info); ?></textarea><br><br>

    <label for="position_x">Позиция X:</label><br>
    <input type="number" name="position_x" value="<?php echo htmlspecialchars($position_x); ?>" required><br><br>

    <label for="position_y">Позиция Y:</label><br>
    <input type="number" name="position_y" value="<?php echo htmlspecialchars($position_y); ?>" required><br><br>

    <button type="submit">Создать комнату</button>
</form>
</body>
</html>