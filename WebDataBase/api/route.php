<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

// Проверка наличия параметров room1 и room2
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['room1']) && isset($_GET['room2'])) {
        $room1 = $_GET['room1'];
        $room2 = $_GET['room2'];

        try {
            // Запрос информации о первой аудитории
            $stmt1 = $conn->prepare("SELECT room_number, floor_id, building_id, position_x, position_y FROM rooms WHERE room_number = :room1");
            $stmt1->bindParam(':room1', $room1);
            $stmt1->execute();
            $room1_data = $stmt1->fetch(PDO::FETCH_ASSOC);

            // Запрос информации о второй аудитории
            $stmt2 = $conn->prepare("SELECT room_number, floor_id, building_id, position_x, position_y FROM rooms WHERE room_number = :room2");
            $stmt2->bindParam(':room2', $room2);
            $stmt2->execute();
            $room2_data = $stmt2->fetch(PDO::FETCH_ASSOC);

            // Проверка, найдены ли обе аудитории
            if ($room1_data && $room2_data) {
                // Формирование ответа
                $response = [
                    'room1' => [
                        'room_number' => $room1_data['room_number'],
                        'floor_id' => $room1_data['floor_id'],
                        'building_id' => $room1_data['building_id'],
                        'position_x' => $room1_data['position_x'],
                        'position_y' => $room1_data['position_y']
                    ],
                    'room2' => [
                        'room_number' => $room2_data['room_number'],
                        'floor_id' => $room2_data['floor_id'],
                        'building_id' => $room2_data['building_id'],
                        'position_x' => $room2_data['position_x'],
                        'position_y' => $room2_data['position_y']
                    ]
                ];
                header('Content-Type: application/json');
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else {
                // Одна из аудиторий не найдена
                http_response_code(404);
                echo json_encode(['error' => 'Одна из аудиторий не найдена', JSON_UNESCAPED_UNICODE]);
            }
        } catch (PDOException $e) {
            // Ошибка базы данных
            http_response_code(500);
            echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // Не указаны номера аудиторий
        http_response_code(400);
        echo json_encode(['error' => 'Не указаны номера аудиторий'], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Недопустимый метод запроса
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается'], JSON_UNESCAPED_UNICODE);
}
?>

<!-- http://localhost/WebDataBase/api/route.php?room1=101&room2=202 -->