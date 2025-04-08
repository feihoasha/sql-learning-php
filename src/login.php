<?php
session_start();

$host = '10.5.0.6';
$dbname = 'sakila';
$username = 'root';
$password = 'rootpass';
//$username = 'login';
//$password = 'fQFds/A7G5VefaBu';

$connection = new mysqli($host, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Ошибка подключения к базе данных: ". $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] = "POST" && isset($_POST['login'])) {
    $username = trim($_POST['name']);
    $password = trim($_POST['password']);

    $query = "SELECT id, name, admin from users WHERE name = ? AND password = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["admin"] = (bool) $user["admin"];
        header("Location: index.php");
        exit();
    }
    else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <header>
    <h1>Вход</h1>
  </header>

  <main>
      <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          <label for = "name">Имя пользователя:</label>
          <input type="text" name="name" id="name" required>
          <label for = "password">Пароль:</label>
          <input type="password" name="password" id="password" required>
          <button type="submit" name="login">Войти</button>
      </form>
      <?php if (isset($error)): ?>
          <p class = "error"><?php echo $error; ?></p>
      <?php endif; ?>
  </main>
</body> 
</html>
