<?php
$settings = json_decode(file_get_contents(__DIR__ . '/data/settings.json'), true);
$siteName = $settings['site_name'] ?? 'AmanitaLove.ru';
$siteDesc = $settings['site_description'] ?? 'Интернет-магазин сушеных грибов премиум-класса';
$siteKeywords = $settings['site_keywords'] ?? 'сушеные грибы, купить грибы';
$phone = $settings['phone'] ?? '+7 (800) 555-19-87';
$email = $settings['email'] ?? 'hello@amanitalove.ru';
$address = $settings['address'] ?? 'г. Москва, ул. Лесная, д. 15';
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

$pageTitle = 'Контакты';
$pageDescription = 'Свяжитесь с AmanitaLove: телефон, email, адрес.';
$canonicalUrl = 'https://amanitalove.ru/contacts';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> — <?php echo $siteName; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="<?php echo $siteKeywords; ?>">
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">
    <meta property="og:title" content="<?php echo $pageTitle; ?> — <?php echo $siteName; ?>">
    <meta property="og:description" content="<?php echo $pageDescription; ?>">
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo $pageTitle; ?> — <?php echo $siteName; ?>">
    <meta name="twitter:description" content="<?php echo $pageDescription; ?>">
    <link rel="icon" href="<?php echo $faviconUrl ?: '/uploads/favicon.ico'; ?>" type="image/x-icon" id="dynamic-favicon">
    <link rel="stylesheet" href="/tailwind-output.css">
    <link rel="stylesheet" href="/themes.css">
    <link rel="stylesheet" href="/styles.css">

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
                <a class="nav-link active" href="/contacts">Контакты</a>
            </nav>
            <div class="flex items-center gap-3">
                <button class="cart-btn" onclick="toggleCart()">🛒<span class="cart-badge" id="cartBadge" style="display:none;">0</span></button>
                <button class="mobile-hamburger text-2xl bg-transparent border-none cursor-pointer text-[var(--text-primary)] p-2" onclick="toggleMobileMenu()" style="display:none;">☰</button>
            </div>
        </div>
    </header>

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

    <main class="py-12 px-4 max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-black mb-2 text-[var(--text-primary)]">📬 Контакты</h1>
        <p class="text-[var(--text-secondary)] mb-8">Свяжитесь с нами — мы всегда рады помочь!</p>

        <div class="bg-[var(--bg-card)] border border-[var(--border)] rounded-[var(--radius-lg)] p-6 md:p-10 mb-10">
            <h2 class="text-xl font-bold mb-6 text-[var(--text-primary)]">✉️ Форма обратной связи</h2>
            <form id="contactForm" class="space-y-5" novalidate>
                <div><label for="contactName" class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Ваше имя *</label><input type="text" id="contactName" required class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-[var(--radius-sm)] text-[var(--text-primary)] focus:outline-none focus:border-[var(--accent)] transition-colors" placeholder="Иван Петров"></div>
                <div><label for="contactEmail" class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Email *</label><input type="email" id="contactEmail" required class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-[var(--radius-sm)] text-[var(--text-primary)] focus:outline-none focus:border-[var(--accent)] transition-colors" placeholder="example@mail.ru"></div>
                <div><label for="contactMessage" class="block text-sm font-medium text-[var(--text-secondary)] mb-1">Сообщение *</label><textarea id="contactMessage" rows="5" required class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-[var(--radius-sm)] text-[var(--text-primary)] focus:outline-none focus:border-[var(--accent)] transition-colors resize-vertical" placeholder="Ваше сообщение..."></textarea></div>
                <button type="submit" class="btn-primary w-full sm:w-auto">📨 Отправить сообщение</button>
                <p class="text-sm text-[var(--success)] mt-3 hidden" id="contactSuccess">✅ Сообщение успешно отправлено! Мы ответим в течение 24 часов.</p>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="text-center p-5 bg-[var(--bg-card)] border border-[var(--border)] rounded-[var(--radius-md)]"><div class="text-3xl mb-2">📧</div><h3 class="font-semibold text-[var(--text-primary)]">Email</h3><p class="text-[var(--text-secondary)] text-sm"><?php echo $email; ?></p></div>
            <div class="text-center p-5 bg-[var(--bg-card)] border border-[var(--border)] rounded-[var(--radius-md)]"><div class="text-3xl mb-2">📞</div><h3 class="font-semibold text-[var(--text-primary)]">Телефон</h3><p class="text-[var(--text-secondary)] text-sm"><?php echo $phone; ?></p></div>
            <div class="text-center p-5 bg-[var(--bg-card)] border border-[var(--border)] rounded-[var(--radius-md)]"><div class="text-3xl mb-2">📍</div><h3 class="font-semibold text-[var(--text-primary)]">Адрес</h3><p class="text-[var(--text-secondary)] text-sm"><?php echo $address; ?></p></div>
            <div class="text-center p-5 bg-[var(--bg-card)] border border-[var(--border)] rounded-[var(--radius-md)]"><div class="text-3xl mb-2">🕐</div><h3 class="font-semibold text-[var(--text-primary)]">Время работы</h3><p class="text-[var(--text-secondary)] text-sm"><?php echo $hours; ?></p></div>
        </div>
    </main>

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
                    <div class="flex gap-3 text-2xl" id="footer-social">
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
                    document.getElementById('dynamic-favicon').href = settings.favicon_url;
                }
                if (settings.site_name) {
                    document.getElementById('footer-site-name').textContent = settings.site_name;
                }
                if (settings.phone) {
                    document.getElementById('footer-phone').innerHTML = '📞 ' + settings.phone;
                }
                if (settings.email) {
                    document.getElementById('footer-email').innerHTML = '📧 ' + settings.email;
                }
                if (settings.hours) {
                    document.getElementById('footer-hours').textContent = settings.hours;
                }
            } catch(e) {}
        }
        applySettings();
    </script>
</body>
</html>