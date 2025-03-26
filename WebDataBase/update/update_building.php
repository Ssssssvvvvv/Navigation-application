<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) {
        $building_id = $_GET['id'];
        
        try {
            $stmt = $conn->prepare("SELECT * FROM buildings WHERE building_id=:building_id");
            $stmt->bindParam(':building_id', $building_id);
            $stmt->execute();
            $building = $stmt->fetch();
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        header("Location: ../read/read_buildings.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['building_name']) && !empty($_POST['building_id'])) {
        $building_name = $_POST['building_name'];
        $building_id = $_POST['building_id'];
        
        try {
            $stmt = $conn->prepare("UPDATE buildings SET building_name=:building_name WHERE building_id=:building_id");
            $stmt->bindParam(':building_name', $building_name);
            $stmt->bindParam(':building_id', $building_id);
            
            if ($stmt->execute()) {
                echo "Здание успешно обновлено.";
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Ошибка при обновлении здания: " . $errorInfo[2];
            }
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        echo "Пожалуйста, введите новое название здания.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Обновление здания</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
<h1>Обновление здания</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="building_id" value="<?php echo $building_id; ?>">
    <label for="building_name">Новое название здания:</label><br>
    <input type="text" name="building_name" value="<?php echo $building['building_name']; ?>"><br><br>
    <button type="submit">Обновить здание</button>
</form>

</body>
</html>