<?php
session_start();
require_once '../config.php';

// Проверка авторизации и роли администратора
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Обработка GET-запроса для получения данных этажа
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) {
        $floor_id = $_GET['id'];
        
        try {
            // Получение данных этажа по ID
            $stmt = $conn->prepare("SELECT * FROM floors WHERE floor_id = :floor_id");
            $stmt->bindParam(':floor_id', $floor_id);
            $stmt->execute();
            $floor = $stmt->fetch();
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        // Если ID не указан, перенаправляем на страницу списка этажей
        header("Location: ../read/read_floors.php");
        exit;
    }
}

// Обработка POST-запроса для обновления данных этажа
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['floor_number']) && !empty($_POST['building_id']) && !empty($_POST['floor_id'])) {
        $floor_number = $_POST['floor_number'];
        $building_id = $_POST['building_id'];
        $floor_id = $_POST['floor_id'];

        // Проверка существования building_id в базе данных
        $stmt = $conn->prepare("SELECT building_id FROM buildings WHERE building_id = :building_id");
        $stmt->bindParam(':building_id', $building_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            try {
                // Обновление данных этажа
                $stmt = $conn->prepare("UPDATE floors SET floor_number = :floor_number, building_id = :building_id WHERE floor_id = :floor_id");
                $stmt->bindParam(':floor_number', $floor_number);
                $stmt->bindParam(':building_id', $building_id);
                $stmt->bindParam(':floor_id', $floor_id);

                if ($stmt->execute()) {
                    echo "Этаж успешно обновлен.";
                } else {
                    echo "Ошибка при обновлении этажа.";
                }
            } catch (PDOException $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        } else {
            echo "Корпус с указанным ID не существует.";
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
    <title>Обновление этажа</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
<h1>Обновление этажа</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <!-- Скрытое поле для передачи ID этажа -->
    <input type="hidden" name="floor_id" value="<?php echo $floor_id; ?>">

    <label for="floor_number">Номер этажа:</label><br>
    <input type="number" name="floor_number" value="<?php echo $floor['floor_number']; ?>" required><br><br>

    <label for="building_id">ID корпуса:</label><br>
    <input type="number" name="building_id" value="<?php echo $floor['building_id']; ?>" required><br><br>

    <button type="submit">Обновить этаж</button>
</form>

</body>
</html>