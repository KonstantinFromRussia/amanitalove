<?php
// --- Чтение глобальных настроек ---
$settings = json_decode(file_get_contents(__DIR__ . '/data/settings.json'), true);
$siteName = $settings['site_name'] ?? 'AmanitaLove.ru';
$siteDescGlobal = $settings['site_description'] ?? 'Интернет-магазин сушеных грибов премиум-класса';
$siteKeywordsGlobal = $settings['site_keywords'] ?? 'сушеные грибы, купить грибы';
$phone = $settings['phone'] ?? '+7 (800) 555-19-87';
$email = $settings['email'] ?? 'hello@amanitalove.ru';
$hours = $settings['hours'] ?? 'Пн–Вс: 9:00–21:00';
$telegram = $settings['telegram'] ?? '';
$vk = $settings['vk'] ?? '';
$youtube = $settings['youtube'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$logoUrl = $settings['logo_url'] ?? '';
$faviconUrl = $settings['favicon_url'] ?? '';
$gaId = $settings['ga_id'] ?? '';
$ymId = $settings['ym_id'] ?? '';
$gscTag = $settings['gsc_tag'] ?? '';
$ywmTag = $settings['ywm_tag'] ?? '';

// --- Определяем тип и ищем запись ---
$type = $_GET['type'] ?? '';
$slug = $_GET['slug'] ?? '';
$id   = (int)($_GET['id'] ?? 0);

$allowed = ['product', 'blog', 'news'];
if (!in_array($type, $allowed)) {
    http_response_code(404);
    die('Страница не найдена');
}

$dataDir = __DIR__ . '/data/';
$fileName = ($type === 'product' ? 'products' : ($type === 'blog' ? 'blog' : 'news')) . '.json';
$filePath = $dataDir . $fileName;

if (!file_exists($filePath)) {
    http_response_code(404);
    die('Данные не найдены');
}

$items = json_decode(file_get_contents($filePath), true);
if (!is_array($items)) {
    http_response_code(500);
    die('Ошибка данных');
}

$item = null;
if (!empty($slug)) {
    foreach ($items as $i) {
        if (($i['slug'] ?? '') === $slug) {
            $item = $i;
            break;
        }
    }
}
if (!$item && $id > 0) {
    foreach ($items as $i) {
        if ((int)$i['id'] === $id) {
            $item = $i;
            break;
        }
    }
}

if (!$item) {
    http_response_code(404);
    die('Запись не найдена');
}

$baseUrl = 'https://amanitalove.ru';

// SEO-данные для записи
$pageTitle = '';
$pageDescription = '';
$pageImage = '';
$pageUrl = '';
$pageKeywords = '';
$imageAlt = '';

if ($type === 'product') {
    $pageTitle = $item['name'];
    $desc = !empty($item['desc']) ? $item['desc'] : strip_tags($item['details'] ?? '');
    $pageDescription = mb_substr($desc, 0, 160);
    $pageImage = !empty($item['image']) ? $baseUrl . $item['image'] : '';
    $pageUrl = $baseUrl . '/product/' . ($item['slug'] ?? $item['id']) . '.html';
    $imageAlt = $item['alt'] ?? $item['name'];
    $pageKeywords = !empty($item['keywords']) ? $item['keywords'] : $siteKeywordsGlobal . ', ' . $item['name'];
} elseif ($type === 'blog') {
    $pageTitle = $item['title'];
    $desc = !empty($item['excerpt']) ? $item['excerpt'] : strip_tags($item['content'] ?? '');
    $pageDescription = mb_substr($desc, 0, 160);
    $pageImage = !empty($item['image']) ? $baseUrl . $item['image'] : '';
    $pageUrl = $baseUrl . '/blog/' . ($item['slug'] ?? $item['id']) . '.html';
    $imageAlt = $item['alt'] ?? $item['title'];
    $pageKeywords = !empty($item['keywords']) ? $item['keywords'] : $siteKeywordsGlobal . ', ' . $item['title'];
} elseif ($type === 'news') {
    $pageTitle = $item['title'];
    $desc = !empty($item['excerpt']) ? $item['excerpt'] : strip_tags($item['content'] ?? '');
    $pageDescription = mb_substr($desc, 0, 160);
    $pageImage = '';
    $pageUrl = $baseUrl . '/news/' . ($item['slug'] ?? $item['id']) . '.html';
    $pageKeywords = !empty($item['keywords']) ? $item['keywords'] : $siteKeywordsGlobal . ', ' . $item['title'];
}

// Schema.org
$schema = '';
if ($type === 'product') {
    $schema = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Product","name":"' . addslashes($item['name']) . '","description":"' . addslashes($pageDescription) . '","image":"' . $pageImage . '","offers":{"@type":"Offer","price":"' . $item['price'] . '","priceCurrency":"RUB","availability":"https://schema.org/InStock"}}</script>';
} elseif ($type === 'blog' || $type === 'news') {
    $schema = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Article","headline":"' . addslashes($item['title']) . '","description":"' . addslashes($pageDescription) . '","image":"' . $pageImage . '","datePublished":"' . ($item['date'] ?? '') . '"}</script>';
}
$breadcrumbs = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Главная","item":"' . $baseUrl . '/"},{"@type":"ListItem","position":2,"name":"' . ($type === 'product' ? 'Каталог' : ($type === 'blog' ? 'Блог' : 'Новости')) . '","item":"' . $baseUrl . '/' . ($type === 'product' ? 'catalog' : $type) . '"},{"@type":"ListItem","position":3,"name":"' . addslashes($pageTitle) . '"}]}</script>';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($siteName); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <link rel="canonical" href="<?php echo $pageUrl; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo $pageUrl; ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($pageImage)): ?>
    <meta property="og:image" content="<?php echo $pageImage; ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($siteName); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <?php if (!empty($pageImage)): ?>
    <meta name="twitter:image" content="<?php echo $pageImage; ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/tailwind-output.css">
    <link rel="stylesheet" href="/themes.css">
    <link rel="stylesheet" href="/styles.css">
    <?php echo $schema; ?>
    <?php echo $breadcrumbs; ?>

    <!-- Метатеги верификации -->
    <?php if (!empty($gscTag)): ?>
    <meta name="google-site-verification" content="<?php echo htmlspecialchars($gscTag); ?>">
    <?php endif; ?>
    <?php if (!empty($ywmTag)): ?>
    <meta name="yandex-verification" content="<?php echo htmlspecialchars($ywmTag); ?>">
    <?php endif; ?>

    <!-- Google Analytics -->
    <?php if (!empty($gaId)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($gaId); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($gaId); ?>');
    </script>
    <?php endif; ?>

    <!-- Яндекс.Метрика -->
    <?php if (!empty($ymId)): ?>
    <script>
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        ym(<?php echo (int)$ymId; ?>, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/<?php echo (int)$ymId; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <?php endif; ?>
</head>
<body>

<!-- ШАПКА (аналогично catalog.php) -->
<header class="site-header" id="siteHeader">
    <div class="max-w-7xl mx-auto flex items-center justify-between py-3">
        <a href="/" class="flex items-center gap-2 text-xl font-bold text-[var(--text-primary)] no-underline logo-container" id="logoContainer">
            <?php if ($logoUrl): ?>
                <img src="<?php echo $logoUrl; ?>" alt="Логотип" style="width:100%; height:auto; object-fit:contain;">
            <?php else: ?>
                <span class="text-2xl">🍄</span>
                <span><?php echo $siteName; ?></span>
            <?php endif; ?>
        </a>
        <nav class="desktop-nav items-center gap-6" style="display: flex;">
            <a class="nav-link" href="/">Главная</a>
            <a class="nav-link" href="/catalog">Каталог</a>
            <a class="nav-link" href="/blog">Блог</a>
            <a class="nav-link" href="/news">Новости</a>
            <a class="nav-link" href="/contacts">Контакты</a>
        </nav>
        <div class="flex items-center gap-3">
            <button class="cart-btn" onclick="toggleCart()">🛒<span class="cart-badge" id="cartBadge" style="display:none;">0</span></button>
            <button class="mobile-hamburger text-2xl bg-transparent border-none cursor-pointer text-[var(--text-primary)] p-2" onclick="toggleMobileMenu()" style="display:none;">☰</button>
        </div>
    </div>
</header>

<!-- Мобильное меню -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="toggleMobileMenu()"></div>
<div class="mobile-menu" id="mobileMenu">
    <div class="flex items-center justify-between mb-4">
        <span class="text-lg font-bold">🍄 <?php echo $siteName; ?></span>
        <button class="text-2xl bg-transparent border-none cursor-pointer text-[var(--text-primary)]" onclick="toggleMobileMenu()">✕</button>
    </div>
    <a class="nav-link text-lg" href="/">Главная</a>
    <a class="nav-link text-lg" href="/catalog">Каталог</a>
    <a class="nav-link text-lg" href="/blog">Блог</a>
    <a class="nav-link text-lg" href="/news">Новости</a>
    <a class="nav-link text-lg" href="/contacts">Контакты</a>
    <button class="btn-outline mt-4 w-full" onclick="toggleCart()">🛒 Корзина <span id="mobileCartCount"></span></button>
</div>

<!-- ОСНОВНОЙ КОНТЕНТ (бывший page.php) -->
<main>
    <?php
    if ($type === 'product') {
        $imgTag = '';
        if (!empty($item['image'])) {
            $alt = htmlspecialchars($imageAlt);
            $imgTag = '<img src="' . htmlspecialchars($item['image']) . '" alt="' . $alt . '" class="w-full h-auto rounded-[var(--radius-md)] mb-8">';
        }
        echo '
        <div class="max-w-4xl mx-auto py-12 px-4">
            <nav class="text-sm text-[var(--text-muted)] mb-6">
                <a href="/" class="hover:text-[var(--accent)]">Главная</a> /
                <a href="/catalog" class="hover:text-[var(--accent)]">Каталог</a> /
                <span>' . htmlspecialchars($item['name']) . '</span>
            </nav>
            <span class="text-[var(--accent)] text-sm uppercase tracking-wide">' . htmlspecialchars($item['latin'] ?? '') . '</span>
            <h1 class="text-3xl md:text-4xl font-bold mt-2 mb-4">' . htmlspecialchars($item['name']) . '</h1>
            ' . $imgTag . '
            <div class="flex flex-wrap gap-4 items-center mb-8">
                <span class="text-3xl font-bold text-[var(--accent)]">' . number_format($item['price'], 0, ',', ' ') . ' ₽</span>
                <span class="text-[var(--text-muted)] text-sm px-3 py-1 bg-[var(--bg-elevated)] rounded-full">' . htmlspecialchars($item['category'] ?? '') . '</span>
            </div>
            <p class="text-[var(--text-secondary)] mb-8 text-lg">' . nl2br(htmlspecialchars($item['desc'] ?? '')) . '</p>
            ' . ($item['details'] ?? '') . '
            <div class="mt-8">
                <button onclick="history.back()" class="btn-outline">← Назад</button>
            </div>
        </div>';
    } elseif ($type === 'blog') {
        $imgTag = '';
        if (!empty($item['image'])) {
            $alt = htmlspecialchars($imageAlt);
            $imgTag = '<img src="' . htmlspecialchars($item['image']) . '" alt="' . $alt . '" class="w-full h-auto rounded-[var(--radius-md)] mb-8">';
        }
        echo '
        <div class="max-w-3xl mx-auto py-12 px-4">
            <nav class="text-sm text-[var(--text-muted)] mb-6">
                <a href="/" class="hover:text-[var(--accent)]">Главная</a> /
                <a href="/blog" class="hover:text-[var(--accent)]">Блог</a> /
                <span>' . htmlspecialchars($item['title']) . '</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold mb-2">' . htmlspecialchars($item['title']) . '</h1>
            <p class="text-[var(--text-muted)] mb-8">' . htmlspecialchars($item['date'] ?? '') . '</p>
            ' . $imgTag . '
            <div class="text-[var(--text-secondary)] leading-relaxed">' . ($item['content'] ?? '') . '</div>
            <div class="mt-8">
                <button onclick="history.back()" class="btn-outline">← Назад</button>
            </div>
        </div>';
    } elseif ($type === 'news') {
        echo '
        <div class="max-w-3xl mx-auto py-12 px-4">
            <nav class="text-sm text-[var(--text-muted)] mb-6">
                <a href="/" class="hover:text-[var(--accent)]">Главная</a> /
                <a href="/news" class="hover:text-[var(--accent)]">Новости</a> /
                <span>' . htmlspecialchars($item['title']) . '</span>
            </nav>
            <h1 class="text-3xl md:text-4xl font-bold mb-2">' . htmlspecialchars($item['title']) . '</h1>
            <p class="text-[var(--text-muted)] mb-8">' . htmlspecialchars($item['date'] ?? '') . '</p>
            <div class="text-[var(--text-secondary)] leading-relaxed">' . ($item['content'] ?? '') . '</div>
            <div class="mt-8">
                <button onclick="history.back()" class="btn-outline">← Назад</button>
            </div>
        </div>';
    }
    ?>
</main>

<!-- КОРЗИНА (сайдбар) -->
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<aside class="cart-sidebar" id="cartSidebar">
    <div class="flex items-center justify-between p-4 border-b border-[var(--border)]">
        <h2 class="text-lg font-bold text-[var(--text-primary)]">🛒 Корзина</h2>
        <button class="text-xl bg-transparent border-none cursor-pointer text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors" onclick="toggleCart()">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto p-4" id="cartItemsContainer">
        <p class="text-[var(--text-muted)] text-center mt-12">Ваша корзина пока пуста 🍄</p>
    </div>
    <div class="p-4 border-t border-[var(--border)]" id="cartFooter" style="display:none;">
        <div class="flex justify-between items-center mb-3">
            <span class="text-[var(--text-secondary)]">Итого:</span>
            <span class="text-xl font-bold text-[var(--accent)]" id="cartTotal">0 ₽</span>
        </div>
        <button class="btn-primary w-full" onclick="checkout()">📦 Оформить заказ</button>
        <button class="text-sm text-[var(--text-muted)] bg-transparent border-none cursor-pointer mt-2 w-full hover:text-[var(--danger)] transition-colors" onclick="clearCart()">Очистить корзину</button>
    </div>
</aside>

<!-- ФУТЕР -->
<footer class="border-t border-[var(--border)] bg-[var(--bg-secondary)] mt-12">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div><h3 class="text-lg font-bold mb-3 text-[var(--text-primary)]">🍄 <?php echo $siteName; ?></h3><p class="text-[var(--text-secondary)] text-sm leading-relaxed">Интернет-магазин премиальных сушеных грибов. Качество, проверенное временем и лабораториями.</p></div>
            <div>
                <h4 class="font-semibold mb-3 text-[var(--text-primary)]">Навигация</h4>
                <ul class="space-y-2 text-sm">
                    <li><a class="footer-link" href="/catalog">Каталог</a></li>
                    <li><a class="footer-link" href="/blog">Блог</a></li>
                    <li><a class="footer-link" href="/news">Новости</a></li>
                    <li><a class="footer-link" href="/contacts">Контакты</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-[var(--text-primary)]">Поддержка</h4>
                <ul class="space-y-2 text-sm">
                    <li><span class="text-[var(--text-secondary)]">📧 <?php echo $email; ?></span></li>
                    <li><span class="text-[var(--text-secondary)]">📞 <?php echo $phone; ?></span></li>
                    <li><span class="text-[var(--text-secondary)]">🕐 <?php echo $hours; ?></span></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-[var(--text-primary)]">Мы в соцсетях</h4>
                <div class="flex gap-3 text-2xl">
                    <?php if ($telegram): ?><a href="<?php echo $telegram; ?>" target="_blank" title="Telegram">📱</a><?php endif; ?>
                    <?php if ($vk): ?><a href="<?php echo $vk; ?>" target="_blank" title="VK">💬</a><?php endif; ?>
                    <?php if ($youtube): ?><a href="<?php echo $youtube; ?>" target="_blank" title="YouTube">🎬</a><?php endif; ?>
                    <?php if ($whatsapp): ?><a href="<?php echo $whatsapp; ?>" target="_blank" title="WhatsApp">💚</a><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="border-t border-[var(--border)] mt-8 pt-6 text-center text-sm text-[var(--text-muted)]">© 2024 <?php echo $siteName; ?> — Все права защищены. Не является публичной офертой.</div>
    </div>
</footer>

<!-- Модальное окно заказа -->
<div class="modal-overlay" id="order-modal-overlay" onclick="closeOrderModal()"></div>
<div class="modal" id="order-modal">
    <button class="modal__close" onclick="closeOrderModal()">✕</button>
    <div class="modal__inner">
        <h2>Оформление заказа</h2>
        <form id="order-form" onsubmit="event.preventDefault(); submitOrder();" style="display:flex; flex-direction:column; gap:0.8rem; margin-top:1rem;">
            <input type="text" id="order-name" placeholder="Имя *" required>
            <input type="tel" id="order-phone" placeholder="Телефон *" required>
            <input type="email" id="order-email" placeholder="Email">
            <input type="text" id="order-address" placeholder="Адрес доставки">
            <textarea id="order-comment" rows="2" placeholder="Комментарий"></textarea>
            <button type="submit" class="btn-primary">📦 Отправить заказ</button>
        </form>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<button id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Наверх">⬆</button>

<script src="script.js" defer></script>
<script>
    async function applySettings() {
        try {
            const res = await fetch('/api/settings');
            const settings = await res.json();
            document.documentElement.setAttribute('data-theme', settings.theme || 'dark');
            const logoContainer = document.querySelector('.logo-container');
            if (logoContainer) {
                if (settings.logo_url) {
                    logoContainer.innerHTML = `<img src="${settings.logo_url}" alt="Логотип" style="width:100%; height:auto; object-fit:contain;">`;
                } else {
                    logoContainer.innerHTML = `<span class="text-2xl">🍄</span><span>${settings.site_name || '<?php echo $siteName; ?>'}</span>`;
                }
            }
            if (settings.favicon_url) {
                const favicon = document.querySelector("link[rel='icon']");
                if (favicon) favicon.href = settings.favicon_url;
            }
        } catch(e) {}
    }
    applySettings();
</script>
</body>
</html>