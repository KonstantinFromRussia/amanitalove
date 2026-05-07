<?php
$settings = json_decode(file_get_contents(__DIR__ . '/data/settings.json'), true);
$siteName = $settings['site_name'] ?? 'AmanitaLove.ru';
$siteDesc = $settings['site_description'] ?? 'Интернет-магазин сушеных грибов премиум-класса';
$siteKeywords = $settings['site_keywords'] ?? 'сушеные грибы, купить грибы';
$phone = $settings['phone'] ?? '+7 (800) 555-19-87';
$email = $settings['email'] ?? 'hello@amanitalove.ru';
$hours = $settings['hours'] ?? 'Пн–Вс: 9:00–21:00';
$telegram = $settings['telegram'] ?? '';
$vk = $settings['vk'] ?? '';
$youtube = $settings['youtube'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$logoUrl = $settings['logo_url'] ?? '';
$faviconUrl = $settings['favicon_url'] ?? '';
$showAdvantages = $settings['show_advantages'] ?? 'yes';
$gaId = $settings['ga_id'] ?? '';
$ymId = $settings['ym_id'] ?? '';
$gscTag = $settings['gsc_tag'] ?? '';
$ywmTag = $settings['ywm_tag'] ?? '';

$pageTitle = 'AmanitaLove.ru — Сушеные грибы премиум‑класса';
$pageDescription = 'Интернет-магазин сушеных грибов премиум-класса. Органические грибы, бережная сушка, доставка по России.';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="<?php echo $siteKeywords; ?>">
    <link rel="canonical" href="https://amanitalove.ru/">
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="<?php echo $pageDescription; ?>">
    <meta property="og:url" content="https://amanitalove.ru/">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $pageTitle; ?>">
    <meta name="twitter:description" content="<?php echo $pageDescription; ?>">
    <link rel="icon" href="<?php echo $faviconUrl ?: '/uploads/favicon.ico'; ?>" type="image/x-icon" id="dynamic-favicon">
    <link rel="stylesheet" href="tailwind-output.css">
    <link rel="stylesheet" href="themes.css">
    <link rel="stylesheet" href="styles.css">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "AmanitaLove.ru",
      "url": "https://amanitalove.ru/",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+7-800-555-19-87",
        "contactType": "customer service"
      }
    }
    </script>

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
    <!-- ==================== ШАПКА ==================== -->
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
                <a class="nav-link active" href="/">Главная</a>
                <a class="nav-link" href="/catalog">Каталог</a>
                <a class="nav-link" href="/blog">Блог</a>
                <a class="nav-link" href="/news">Новости</a>
                <a class="nav-link" href="/contacts">Контакты</a>
            </nav>
            <div class="flex items-center gap-3">
                <button class="cart-btn" onclick="toggleCart()" aria-label="Открыть корзину" title="Корзина">
                    🛒
                    <span class="cart-badge" id="cartBadge" style="display:none;">0</span>
                </button>
                <button class="mobile-hamburger text-2xl bg-transparent border-none cursor-pointer text-[var(--text-primary)] p-2" onclick="toggleMobileMenu()" aria-label="Меню" style="display:none;">☰</button>
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

    <!-- ==================== ГЛАВНАЯ СТРАНИЦА ==================== -->
    <main>
        <!-- Hero -->
        <section class="hero-section">
            <div class="hero-glow hero-glow--amber"></div>
            <div class="hero-glow hero-glow--green"></div>
            <div class="relative z-10 max-w-4xl mx-auto">
                <span class="inline-block text-[var(--accent)] font-semibold text-sm uppercase tracking-widest mb-3 fade-in-section">Премиум-качество с 2020 года</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-6 fade-in-section text-[var(--text-primary)]">Откройте мир<br><span class="text-[var(--accent)]">натуральных</span> сушеных грибов</h1>
                <p class="text-lg md:text-xl text-[var(--text-secondary)] max-w-2xl mx-auto mb-8 fade-in-section leading-relaxed">Бережная сушка, ручной отбор и строгий контроль качества. Мы доставляем лучшие грибы со всего мира прямо к вашему столу.</p>
                <div class="flex flex-wrap gap-3 justify-center fade-in-section">
                    <a href="/catalog" class="btn-primary">🛍 Перейти в каталог</a>
                    <a href="/blog" class="btn-outline">📖 Узнать больше</a>
                </div>
            </div>
        </section>

        <!-- Преимущества -->
        <section class="py-16 px-4 max-w-7xl mx-auto fade-in-section" id="advantages-block">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-12 text-[var(--text-primary)]">Почему выбирают <span class="text-[var(--accent)]">AmanitaLove</span></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="advantage-card"><div class="advantage-icon">🌿</div><h3 class="font-semibold text-lg mb-2 text-[var(--text-primary)]">100% Органика</h3><p class="text-[var(--text-secondary)] text-sm">Все грибы собраны в экологически чистых регионах, без химикатов и пестицидов.</p></div>
                <div class="advantage-card"><div class="advantage-icon">🔬</div><h3 class="font-semibold text-lg mb-2 text-[var(--text-primary)]">Лабораторный контроль</h3><p class="text-[var(--text-secondary)] text-sm">Каждая партия проходит проверку на безопасность и содержание активных веществ.</p></div>
                <div class="advantage-card"><div class="advantage-icon">📦</div><h3 class="font-semibold text-lg mb-2 text-[var(--text-primary)]">Бережная упаковка</h3><p class="text-[var(--text-secondary)] text-sm">Вакуумная упаковка сохраняет свежесть и аромат до 24 месяцев.</p></div>
                <div class="advantage-card"><div class="advantage-icon">🚚</div><h3 class="font-semibold text-lg mb-2 text-[var(--text-primary)]">Быстрая доставка</h3><p class="text-[var(--text-secondary)] text-sm">Отправка в день заказа по всей России. Бесплатно при заказе от 3000₽.</p></div>
            </div>
        </section>

        <!-- Популярные товары -->
        <section class="py-16 px-4 max-w-7xl mx-auto fade-in-section" id="featuredProducts">
            <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-[var(--text-primary)]">🔥 Популярные товары</h2>
                <a href="/catalog" class="btn-outline btn-sm">Смотреть все →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" id="featuredGrid"></div>
        </section>

        <!-- Превью блога -->
        <section class="py-16 px-4 max-w-7xl mx-auto fade-in-section">
            <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-[var(--text-primary)]">📝 Из блога</h2>
                <a href="/blog" class="btn-outline btn-sm">Все статьи →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5" id="blogPreviewGrid"></div>
        </section>

        <!-- Превью новостей -->
        <section class="py-16 px-4 max-w-7xl mx-auto fade-in-section">
            <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-[var(--text-primary)]">📰 Новости магазина</h2>
                <a href="/news" class="btn-outline btn-sm">Все новости →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5" id="newsPreviewGrid"></div>
        </section>
    </main>

    <!-- ==================== САЙДБАР КОРЗИНЫ ==================== -->
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

    <!-- ==================== ФУТЕР ==================== -->
    <footer class="border-t border-[var(--border)] bg-[var(--bg-secondary)] mt-12">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div><h3 class="text-lg font-bold mb-3 text-[var(--text-primary)]"><span id="footer-site-name"><?php echo $siteName; ?></span></h3><p class="text-[var(--text-secondary)] text-sm leading-relaxed">Интернет-магазин премиальных сушеных грибов. Качество, проверенное временем и лабораториями.</p></div>
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
                    <ul class="space-y-2 text-sm" id="footer-contacts">
                        <li><span class="text-[var(--text-secondary)]" id="footer-email">📧 <?php echo $email; ?></span></li>
                        <li><span class="text-[var(--text-secondary)]" id="footer-phone">📞 <?php echo $phone; ?></span></li>
                        <li><span class="text-[var(--text-secondary)]">🕐 <span id="footer-hours"><?php echo $hours; ?></span></span></li>
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
            <div class="border-t border-[var(--border)] mt-8 pt-6 text-center text-sm text-[var(--text-muted)]">© 2024 <span id="footer-copy-name"><?php echo $siteName; ?></span> — Все права защищены. Не является публичной офертой.</div>
        </div>
    </footer>

    <!-- ==================== МОДАЛЬНОЕ ОКНО ОФОРМЛЕНИЯ ЗАКАЗА ==================== -->
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

    <!-- ==================== TOAST-УВЕДОМЛЕНИЯ ==================== -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- ==================== КНОПКА "НАВЕРХ" ==================== -->
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
                    const favicon = document.getElementById('dynamic-favicon');
                    if (favicon) favicon.href = settings.favicon_url;
                }
                if (settings.show_advantages === 'no') {
                    const advBlock = document.getElementById('advantages-block');
                    if (advBlock) advBlock.style.display = 'none';
                }
                const socialDiv = document.getElementById('footer-social');
                if (socialDiv) {
                    socialDiv.innerHTML = '';
                    if (settings.telegram) socialDiv.innerHTML += `<a href="${settings.telegram}" target="_blank" title="Telegram">📱</a>`;
                    if (settings.vk) socialDiv.innerHTML += `<a href="${settings.vk}" target="_blank" title="VK">💬</a>`;
                    if (settings.youtube) socialDiv.innerHTML += `<a href="${settings.youtube}" target="_blank" title="YouTube">🎬</a>`;
                    if (settings.whatsapp) socialDiv.innerHTML += `<a href="${settings.whatsapp}" target="_blank" title="WhatsApp">💚</a>`;
                }
                if (settings.site_name) {
                    const siteNameSpan = document.getElementById('footer-site-name');
                    if (siteNameSpan) siteNameSpan.textContent = settings.site_name;
                    const copySpan = document.getElementById('footer-copy-name');
                    if (copySpan) copySpan.textContent = settings.site_name;
                }
                if (settings.phone) {
                    const phoneSpan = document.getElementById('footer-phone');
                    if (phoneSpan) phoneSpan.innerHTML = '📞 ' + settings.phone;
                }
                if (settings.email) {
                    const emailSpan = document.getElementById('footer-email');
                    if (emailSpan) emailSpan.innerHTML = '📧 ' + settings.email;
                }
                if (settings.hours) {
                    const hoursSpan = document.getElementById('footer-hours');
                    if (hoursSpan) hoursSpan.textContent = settings.hours;
                }
            } catch(e) {
                console.warn("Не удалось загрузить настройки:", e);
            }
        }
        applySettings();
    </script>
</body>
</html>