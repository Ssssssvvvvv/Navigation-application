<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['building_name'])) {
        $building_name = $_POST['building_name'];
        
        try {
            $stmt = $conn->prepare("INSERT INTO buildings (building_name) VALUES (:building_name)");
            $stmt->bindParam(':building_name', $building_name);
            
            if ($stmt->execute()) {
                echo "Здание успешно создано.";
            } else {
                echo "Ошибка при создании здания.";
            }
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        echo "Пожалуйста, введите название здания.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Создание здания</title>
    <link rel="stylesheet" type="text/css" href="../style/style_create.css">

</head>
<body>
    <h1>Создание здания</h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="building_name">Название здания:</label><br>
    <input type="text" name="building_name"><br><br>
    <button type="submit">Создать здание</button>
</form>
</body>
</html>