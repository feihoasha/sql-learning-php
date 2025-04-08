<?php
session_start();

$host = '10.5.0.6';
$dbname = 'sakila';
$username = 'root';
$password = 'rootpass';

$connection = new mysqli($host, $username, $password, $dbname);

// Проверка соединения
if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

// Инициализация массива выполненных запросов, если он не существует
if (!isset($_SESSION['completed_queries'])) {
    $_SESSION['completed_queries'] = [];
}

// Функция для проверки SQL-запросов на наличие опасных операций
function isSafeQuery($query) {
    // Список запрещённых ключевых слов
    $unsafeKeywords = ['DROP', 'DELETE', 'UPDATE', 'ALTER', 'TRUNCATE'];
    foreach ($unsafeKeywords as $keyword) {
        // Поиск ключевого слова в запросе (регистронезависимо)
        if (stripos($query, $keyword) !== false) {
            return false; // Запрос содержит опасное ключевое слово
        }
    }
    return true; // Запрос безопасен
}

echo "<h1>Запрос</h1>";
echo "<nav>";
echo "<ul>";
echo "<li><a href='index.php'>Главная страница</a></li>";
echo "<li><a href='logout.php'>Выход</a></li>";
echo "</ul>";
echo "</nav>";
echo "<h1>SQL Query Executor</h1>
<form method='post' action=".htmlspecialchars($_SERVER['PHP_SELF']).">
      <label for='sql_query'>Введите SQL-запрос:</label>
      <textarea name='sql_query' id='sql_query' rows='3' required></textarea>
      <br>
      <input type='submit' value='Выполнить'>
</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_query = trim($_POST['sql_query']);

    // Проверка запроса на безопасность
    if (!isSafeQuery($sql_query)) {
        echo "<h2>Ошибка: Запрос содержит запрещённые операции (DROP, DELETE, UPDATE и т.д.).</h2>";
    } else {
        try {
            $stmt = $connection->query($sql_query);
            $results = $stmt->fetch_all(MYSQLI_ASSOC);

            echo "<h2>Результаты запроса: $sql_query</h2>";

            $rows = array_keys($results[0]);
            echo "<table border='1'><thead><tr>";
            foreach ($rows as $row) {
                echo "<th>$row</th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($results as $row) {
                echo "<tr>";
                foreach ($rows as $r) {
                    echo "<td>$row[$r]</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Добавление выполненного запроса в сессию
            $_SESSION['completed_queries'][] = $sql_query;
        } catch (Exception $e) {
            echo "<h2>Ошибка выполнения запроса:</h2>";
            echo "<pre>";
            echo $e->getMessage();
            echo "</pre>";
        }
    }
}

// Отображение выполненных запросов
if (!empty($_SESSION['completed_queries'])) {
    echo "<h2>Выполненные запросы:</h2>";
    echo "<ul>";
    foreach ($_SESSION['completed_queries'] as $completed_query) {
        echo "<li>$completed_query</li>";
    }
    echo "</ul>";
}

// Закрытие соединения
$connection->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Выполнить запрос</title>
</head>
</html>
