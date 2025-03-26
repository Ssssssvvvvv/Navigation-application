<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        h1, h2 {
            color: #333;
            text-align: center;
        }
        
        .navbar {
            display: flex;
            justify-content: center;
            background-color: #007bff;
            padding: 10px 0;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .navbar a:hover {
            background-color: #0056b3;
        }
        
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
        }
        
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #28a745; /* Green color for success messages */
        }
        
        .switch-form {
            text-align: center;
            margin-top: 20px;
        }
        
        .switch-link {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }
        
        .switch-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1>Регистрация</h1>

<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            
            if ($stmt->execute()) {
                echo "Учетная запись успешно создана.";
            } else {
                echo "Ошибка при создании учетной записи.";
            }
        } catch (PDOException $e) {
            echo "Это имя уже занято, введите другое";
        }
    } else {
        echo "Пожалуйста, заполните все поля.";
    }
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="username">Имя пользователя:</label><br>
    <input type="text" name="username"><br><br>
    <label for="password">Пароль:</label><br>
    <input type="password" name="password"><br><br>
    <button type="submit">Зарегистрироваться</button>
</form>
<div class="switch-form">
        Уже зарегистрирован? <a href="login.php">Войти</a>
    </div>


</body>
</html>