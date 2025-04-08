<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Обучение SQL</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Обучение SQL</h1>
    <h2>Выберите действие:</h2>
    <nav>
        <ul>
            <li><a href="dynamic.php">Запросы</a></li>
            <li><a href="logout.php">Выход</a></li>
        </ul>
    </nav>
</body>
</html>
