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
    <title>Document</title>
    <style>
        .navbare { 
            display:flex; 
            flex-direction: column; 
            background-color:#409EFF; 
            padding:5px; 
            border-radius:.5em; 
            margin-bottom:.2em; 
        }
        .navbare a { 
            color:black; 
            text-decoration:none; 
            background-color:#73B8FF; 
            padding:.5em; 
            margin:.5em; 
            border-radius:.5em; 
            transition:.3s ease; 
            text-align: center;
        }
        .navbare a:hover { 
            background-color:#0056b3; 
        }
    </style>
</head>
<body class="navbare">
    <a href="index.php" target="content">Главная</a>
    <a href="read/read_buildings.php" target="content">Здания</a>
    <a href="create/create_building.php" target="content">Создать здание</a>
    <a href="read/read_floors.php" target="content">Этажи</a>
    <a href="create/create_floor.php" target="content">Создать этаж</a>
    <a href="read/read_rooms.php" target="content">Комнаты</a>
    <a href="create/create_room.php" target="content">Создать комнату</a>
    <?php
        if ($_SESSION['role'] === 'admin') {
            echo '<a href="read/read_users.php" target="content">Пользователи</a>';
        }?>
    
</body>
</html>