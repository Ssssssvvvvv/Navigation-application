<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

// Проверка наличия параметра room_number
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['room_number'])) {
        $room_number = $_GET['room_number'];

        try {
            // Подготовка SQL-запроса
            $stmt = $conn->prepare("SELECT room_number, room_type, floor_id, building_id, info FROM rooms WHERE room_number = :room_number");
            $stmt->bindParam(':room_number', $room_number);
            $stmt->execute();
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            // Проверка, найдена ли аудитория
            if ($room) {
                // Возврат данных в формате JSON с поддержкой кириллицы
                header('Content-Type: application/json');
                echo json_encode($room, JSON_UNESCAPED_UNICODE);
            } else {
                // Аудитория не найдена
                http_response_code(404);
                echo json_encode(['error' => 'Аудитория не найдена'], JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            // Ошибка базы данных
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // Не указан номер аудитории
        http_response_code(400);
        echo json_encode(['error' => 'Не указан номер аудитории'], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Недопустимый метод запроса
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
}
?>

<!-- http://localhost/WebDataBase/api/room.php?room_number=101 -->