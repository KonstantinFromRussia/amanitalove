<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== ТРАНСЛИТЕРАЦИЯ ==========
function generateSlug($text) {
    $map = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd',
        'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i',
        'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
        'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't',
        'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'ts', 'Ч' => 'ch',
        'Ш' => 'sh', 'Щ' => 'sch', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '',
        'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya'
    ];
    $slug = '';
    $len = mb_strlen($text, 'UTF-8');
    for ($i = 0; $i < $len; $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $slug .= isset($map[$char]) ? $map[$char] : $char;
    }
    $slug = mb_strtolower($slug, 'UTF-8');
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'untitled';
}

$action = $_GET['action'] ?? '';
$dataDir = __DIR__ . '/data/';

$allowed = [
    'products' => 'products.json',
    'blog'     => 'blog.json',
    'news'     => 'news.json',
    'orders'   => 'orders.json',
    'settings' => 'settings.json'
];

if (!array_key_exists($action, $allowed)) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$filePath = $dataDir . $allowed[$action];

// ================== ЗАКАЗЫ ==================
if ($action === 'orders') {
    // GET – получить список заказов (только для админа)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (empty($_SESSION['logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        if (file_exists($filePath)) {
            echo file_get_contents($filePath);
        } else {
            echo json_encode([]);
        }
        exit;
    }

    // POST – создать новый заказ (доступно всем)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input');
        $order = json_decode($raw, true);
        if (!is_array($order) || empty($order['customer']) || empty($order['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid order data']);
            exit;
        }

        $orders = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

        $newId = count($orders) ? max(array_column($orders, 'id')) + 1 : 1;
        $order['id'] = $newId;
        $order['date'] = date('Y-m-d H:i:s');
        $order['status'] = 'Новый';

        $orders[] = $order;

        $pretty = json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (file_put_contents($filePath, $pretty, LOCK_EX) === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to save order']);
            exit;
        }

        // Отправка email владельцу (замените на свой адрес)
        $to = 'your@email.ru';
        $subject = 'Новый заказ на AmanitaLove.ru';
        $message = "Поступил новый заказ №{$newId}\n\n";
        $message .= "Дата: {$order['date']}\n";
        $message .= "Клиент: {$order['customer']['name']}\n";
        $message .= "Телефон: {$order['customer']['phone']}\n";
        $message .= "Email: {$order['customer']['email']}\n";
        $message .= "Адрес: {$order['customer']['address']}\n";
        $message .= "Комментарий: {$order['customer']['comment']}\n\n";
        $message .= "Товары:\n";
        foreach ($order['items'] as $item) {
            $message .= "- {$item['name']} x {$item['qty']} = {$item['price']}₽\n";
        }
        $message .= "\nИтого: {$order['total']}₽";

        $headers = "From: shop@amanitalove.ru\r\nContent-Type: text/plain; charset=utf-8\r\n";
        mail($to, $subject, $message, $headers);

        echo json_encode(['success' => true, 'orderId' => $newId]);
        exit;
    }

    // PUT – обновить заказы (только для админа)
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (empty($_SESSION['logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $raw = file_get_contents('php://input');
        $orders = json_decode($raw, true);
        if (!is_array($orders)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }
        $pretty = json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (file_put_contents($filePath, $pretty, LOCK_EX) !== false) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to save file']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ================== НАСТРОЙКИ ==================
if ($action === 'settings') {
    // GET – получить настройки (доступно всем)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (file_exists($filePath)) {
            echo file_get_contents($filePath);
        } else {
            echo json_encode(['theme' => 'dark']);
        }
        exit;
    }

    // PUT – сохранить настройки (только для админа)
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (empty($_SESSION['logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $raw = file_get_contents('php://input');
        $settings = json_decode($raw, true);
        if (!is_array($settings)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        // Автоматический robots.txt: если поле пустое, подставляем стандартный шаблон
        if (empty($settings['robots'])) {
            $settings['robots'] = "User-agent: *\nDisallow: /admin/\nDisallow: /api/\nDisallow: /data/\nSitemap: https://amanitalove.ru/sitemap.xml";
        }

        $pretty = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (file_put_contents($filePath, $pretty, LOCK_EX) !== false) {
            // Записываем robots.txt в корень сайта
            file_put_contents(__DIR__ . '/robots.txt', $settings['robots'], LOCK_EX);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to save settings']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ================== ТОВАРЫ / БЛОГ / НОВОСТИ ==================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($filePath)) {
        echo file_get_contents($filePath);
    } else {
        echo json_encode([]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (empty($_SESSION['logged_in'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    // Автоматическая генерация slug для каждой записи, если он не указан
    foreach ($data as &$item) {
        $titleField = '';
        if ($action === 'products') $titleField = $item['name'] ?? '';
        elseif ($action === 'blog') $titleField = $item['title'] ?? '';
        elseif ($action === 'news') $titleField = $item['title'] ?? '';

        if (!empty($titleField) && empty($item['slug'])) {
            $item['slug'] = generateSlug($titleField);
        }
    }
    unset($item);

    $pretty = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if (file_put_contents($filePath, $pretty, LOCK_EX) !== false) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to write file']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);