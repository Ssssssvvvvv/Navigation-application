<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['floor_number']) && !empty($_POST['building_id'])) {
        $floor_number = $_POST['floor_number'];
        $building_id = $_POST['building_id'];

        $stmt = $conn->prepare("SELECT building_id FROM buildings WHERE building_id = :building_id");
        $stmt->bindParam(':building_id', $building_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            try {
                $stmt = $conn->prepare("INSERT INTO floors (floor_number, building_id) VALUES (:floor_number, :building_id)");
                $stmt->bindParam(':floor_number', $floor_number);
                $stmt->bindParam(':building_id', $building_id);

                if ($stmt->execute()) {
                    echo "Этаж успешно создан.";
                } else {
                    echo "Ошибка при создании этажа.";
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
    <title>Создание этажа</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
<?php include 'head.php'; ?>
<h1>Создание этажа</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="building_id">ID корпуса:</label><br>
    <input type="number" name="building_id" required><br><br>

    <label for="floor_number">Номер этажа:</label><br>
    <input type="number" name="floor_number" required><br><br>

    <button type="submit">Создать этаж</button>
</form>
</body>
</html>