<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) {
        $room_id = $_GET['id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id=:room_id");
            $stmt->bindParam(':room_id', $room_id);
            
            if ($stmt->execute()) {
                echo "Здание успешно удалено.";
            } else {
                echo "Ошибка при удалении здания.";
            }
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        header("Location: ../read/read_rooms.php");
        exit;
    }
}

header("Location: ../read/read_rooms.php");
exit;
?>