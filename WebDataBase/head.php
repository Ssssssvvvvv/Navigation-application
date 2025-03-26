<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDB</title>
    <link rel="stylesheet" type="text/css" href="style/style_head.css">

</head>
<body class="navbar">
    <div class="nav-links">
        <a href="index.php" target="content"><h2>WebDataBase</h2></a>
    </div>
    <?php 
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }        
        catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }?>
    <div class="nav-links">
        <a href="profile.php" span class="username" target="content"><h2><?= $user['username'] ?></h2></span>
        <a href="logout.php" class="logout-button" target="all"><h3>Выход</h3></a>
    </div>
</body>
</html>