<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass  = $_POST['pass'] ?? '';
    // Замените на свои данные
    if ($login === 'admin' && $pass === 'secret123') {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход в админку AmanitaLove</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { background: #0a0a0c; color: #f0ece6; font-family: Roboto; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #18181c; padding: 2rem; border-radius: 14px; border: 1px solid #2a2a30; width: 300px; }
        h2 { margin-top: 0; }
        label { display: block; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.6rem; margin-bottom: 1rem; background: #111114; border: 1px solid #2a2a30; color: #f0ece6; border-radius: 8px; }
        button { background: #c9965a; color: #000; border: none; padding: 0.7rem 2rem; border-radius: 9999px; font-weight: bold; cursor: pointer; width: 100%; }
        .error { color: #d4554a; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Вход</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Логин</label>
        <input type="text" name="login" required>
        <label>Пароль</label>
        <input type="password" name="pass" required>
        <button type="submit">Войти</button>
    </form>
</body>
</html>