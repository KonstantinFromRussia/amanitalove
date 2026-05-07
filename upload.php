<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico'];   // ← добавлен ico

    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo 'Недопустимый тип файла';
        exit;
    }

    // Если это favicon — сохраняем прямо в корень (или рядом с index.html)
    if (isset($_POST['type']) && $_POST['type'] === 'favicon') {
        $dest = __DIR__ . '/favicon.ico';   // всегда одно имя
    } else {
        $filename = uniqid() . '.' . $ext;
        $dest = $uploadDir . $filename;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        if (isset($_POST['type']) && $_POST['type'] === 'favicon') {
            echo '/favicon.ico';
        } else {
            echo '/uploads/' . (isset($filename) ? $filename : basename($dest));
        }
    } else {
        http_response_code(500);
        echo 'Ошибка загрузки';
    }
    exit;
}
?>