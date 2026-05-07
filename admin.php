<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Админ‑панель AmanitaLove</title>
    <link rel="stylesheet" href="tailwind-output.css">
    <link rel="stylesheet" href="themes.css">
    <link rel="stylesheet" href="styles.css">
    <link href="/js/summernote/summernote-lite.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a0c; --bg-secondary: #111114; --bg-card: #18181c;
            --bg-card-hover: #1f1f24; --bg-elevated: #222228; --text-primary: #f0ece6;
            --text-secondary: #b0a99e; --text-muted: #7a7368; --accent: #c9965a;
            --accent-light: #d4a574; --accent-dark: #a67c4a; --border: #2a2a30;
            --border-light: #33333a; --danger: #d4554a; --success: #5a9a5a;
            --radius-sm: 8px; --radius-md: 14px; --radius-lg: 20px; --radius-full: 9999px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Roboto, sans-serif; background: var(--bg-primary); color: var(--text-primary); padding: 1rem; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { margin-bottom: 1.5rem; font-size: 2rem; }
        h2 { margin: 1.5rem 0 1rem; font-size: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; }
        .tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .tab {
            background: var(--bg-card); border: 1px solid var(--border);
            padding: 0.5rem 1.2rem; border-radius: var(--radius-full); cursor: pointer;
            font-weight: 500; color: var(--text-secondary); transition: 0.2s;
        }
        .tab.active { background: var(--accent); color: #000; border-color: var(--accent); }
        .tab:hover:not(.active) { border-color: var(--accent-dark); color: var(--text-primary); }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
        form { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.9rem; margin-bottom: 0.3rem; color: var(--text-secondary); }
        input, textarea, select {
            width: 100%; padding: 0.6rem 0.8rem; background: var(--bg-secondary);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            color: var(--text-primary); font-family: inherit; margin-bottom: 0.8rem;
        }
        textarea { resize: vertical; min-height: 80px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--accent); }
        .note-editor { margin-bottom: 0.8rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.6rem 1.4rem; font-weight: 600; border-radius: var(--radius-full);
            border: none; cursor: pointer; font-size: 0.95rem; transition: 0.2s;
        }
        .btn-primary { background: var(--accent); color: #000; }
        .btn-primary:hover { background: var(--accent-light); }
        .btn-outline { background: transparent; color: var(--accent); border: 2px solid var(--accent); margin-right: 0.5rem; }
        .btn-outline:hover { background: var(--accent); color: #000; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { opacity: 0.9; }
        .btn-sm { padding: 0.4rem 1rem; font-size: 0.8rem; }
        .list-item {
            background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-sm);
            padding: 0.8rem 1rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;
        }
        .list-item .info { flex: 1; }
        .list-item .title { font-weight: 600; }
        .list-item .sub { color: var(--text-muted); font-size: 0.8rem; }
        .actions { display: flex; gap: 0.4rem; }
        .hidden { display: none !important; }
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);
            z-index: 1000; display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius-lg);
            padding: 2rem; max-width: 500px; width: 90%; text-align: center;
        }
        .status-msg {
            padding: 0.5rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; display: none;
        }
        .status-success { background: var(--success); color: #fff; }
        .status-error { background: var(--danger); color: #fff; }
        /* Summernote светлая тема */
        .note-editor.note-frame { border: 1px solid #ccc !important; background: #fff !important; color: #333 !important; }
        .note-editor .note-toolbar { background: #f8f9fa !important; border-bottom: 1px solid #ddd !important; }
        .note-editor .note-toolbar .note-btn { background: #fff !important; color: #333 !important; border: 1px solid #ccc !important; }
        .note-editor .note-toolbar .note-btn:hover { background: #e9ecef !important; }
        .note-editor .note-editing-area { background: #fff !important; color: #333 !important; }
        .note-editor .note-editable { background: #fff !important; color: #333 !important; }
        .note-editor .note-statusbar { background: #f8f9fa !important; border-top: 1px solid #ddd !important; }
        .note-editor .note-statusbar .note-resizebar { background: #f8f9fa !important; }
        .note-editor .note-dropdown-menu { background: #fff !important; border: 1px solid #ccc !important; }
        .note-editor .note-dropdown-item { color: #333 !important; }
        .note-editor .note-dropdown-item:hover { background: #e9ecef !important; }
        .note-modal .note-modal-content { background: #fff !important; color: #333 !important; }
        .note-modal .note-modal-header { background: #f8f9fa !important; border-bottom: 1px solid #ddd !important; }
        .note-modal .note-modal-title { color: #333 !important; }
        .note-modal .note-modal-body { background: #fff !important; }
        .note-modal .note-modal-footer { background: #f8f9fa !important; border-top: 1px solid #ddd !important; }
        .note-modal input, .note-modal textarea, .note-modal select { background: #fff !important; color: #333 !important; border: 1px solid #ccc !important; }
        .note-editable img { width: auto !important; max-width: 100% !important; height: auto !important; }
    </style>
</head>
<body>
<div class="container">
    <h1>⚙️ Админ-панель AmanitaLove</h1>
    <div id="status" class="status-msg"></div>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div class="tabs">
            <button class="tab active" data-tab="product">🍄 Товары</button>
            <button class="tab" data-tab="blog">📝 Блог</button>
            <button class="tab" data-tab="news">📰 Новости</button>
            <button class="tab" data-tab="orders">📦 Заказы</button>
            <button class="tab" data-tab="settings">⚙️ Настройки</button>
        </div>
        <a href="logout.php" style="color:var(--accent); text-decoration:none;">Выход</a>
    </div>

    <!-- ТОВАРЫ -->
    <div class="form-panel active" id="panel-product">
        <form id="product-form">
            <h2>Товар</h2>
            <input type="hidden" id="prod-id">
            <label>Название *</label><input type="text" id="prod-name" required>
            <label>Slug (URL)</label><input type="text" id="prod-slug" placeholder="generiruetsya-avtomaticheski">
            <label>Латинское</label><input type="text" id="prod-latin">
            <label>Категория</label>
            <select id="prod-category">
                <option value="culinary">Кулинарные</option>
                <option value="medicinal">Лечебные</option>
                <option value="exotic">Экзотические</option>
            </select>
            <label>Цена (₽) *</label><input type="number" id="prod-price" required>
            <label>Изображение</label>
            <input type="file" id="prod-image-file" accept="image/*">
            <input type="hidden" id="prod-image-url">
            <label>ALT-текст изображения</label>
            <input type="text" id="prod-alt" placeholder="Описание картинки для SEO">
            <label>Эмодзи (если нет картинки)</label><input type="text" id="prod-emoji" placeholder="🍄">
            <label>Краткое описание</label><textarea id="prod-desc" rows="2"></textarea>
            <label>Градиент (CSS)</label><input type="text" id="prod-gradient" placeholder="linear-gradient(...)">
            <label>Полное описание (HTML)</label><textarea id="prod-details" rows="5"></textarea>
            <label>Ключевые слова (SEO)</label><input type="text" id="prod-keywords" placeholder="белые грибы, купить грибы">
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                <button type="button" class="btn btn-outline" onclick="clearForm('product')">✕ Отмена</button>
            </div>
        </form>
        <h2>Список товаров</h2>
        <div id="products-list"></div>
    </div>

    <!-- БЛОГ -->
    <div class="form-panel" id="panel-blog">
        <form id="blog-form">
            <h2>Запись</h2>
            <input type="hidden" id="blog-id">
            <label>Заголовок *</label><input type="text" id="blog-title" required>
            <label>Slug (URL)</label><input type="text" id="blog-slug" placeholder="generiruetsya-avtomaticheski">
            <label>Краткое описание</label><textarea id="blog-excerpt" rows="2"></textarea>
            <label>Изображение</label>
            <input type="file" id="blog-image-file" accept="image/*">
            <input type="hidden" id="blog-image-url">
            <label>ALT-текст изображения</label>
            <input type="text" id="blog-alt" placeholder="Описание картинки для SEO">
            <label>Эмодзи (если нет картинки)</label><input type="text" id="blog-emoji" placeholder="🍵">
            <label>Дата</label><input type="text" id="blog-date" placeholder="15 ноября 2024">
            <label>Градиент (CSS)</label><input type="text" id="blog-gradient" placeholder="linear-gradient(...)">
            <label>Полный текст (HTML)</label><textarea id="blog-content" rows="6"></textarea>
            <label>Ключевые слова (SEO)</label><input type="text" id="blog-keywords" placeholder="грибы, заваривание, рецепты">
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                <button type="button" class="btn btn-outline" onclick="clearForm('blog')">✕ Отмена</button>
            </div>
        </form>
        <h2>Записи блога</h2>
        <div id="blog-list"></div>
    </div>

    <!-- НОВОСТИ -->
    <div class="form-panel" id="panel-news">
        <form id="news-form">
            <h2>Новость</h2>
            <input type="hidden" id="news-id">
            <label>Заголовок *</label><input type="text" id="news-title" required>
            <label>Slug (URL)</label><input type="text" id="news-slug" placeholder="generiruetsya-avtomaticheski">
            <label>Краткое описание</label><textarea id="news-excerpt" rows="2"></textarea>
            <label>Дата</label><input type="text" id="news-date" placeholder="10 ноября 2024">
            <label>Полный текст (HTML)</label><textarea id="news-content" rows="6"></textarea>
            <label>Ключевые слова (SEO)</label><input type="text" id="news-keywords" placeholder="акции, новости, события">
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                <button type="button" class="btn btn-outline" onclick="clearForm('news')">✕ Отмена</button>
            </div>
        </form>
        <h2>Новости</h2>
        <div id="news-list"></div>
    </div>

    <!-- ЗАКАЗЫ -->
    <div class="form-panel" id="panel-orders">
        <h2>Заказы</h2>
        <div id="orders-list"></div>
    </div>

    <!-- НАСТРОЙКИ -->
    <div class="form-panel" id="panel-settings">
        <h2>Настройки сайта</h2>
        <form id="settings-form">
            <h3>Тема</h3>
            <label>Тема оформления</label>
            <select id="settings-theme">
                <option value="dark">Тёмная</option>
                <option value="light">Светлая</option>
                <option value="forest">Лесная</option>
            </select>

            <h3>SEO (общие)</h3>
            <label>Название сайта</label>
            <input type="text" id="settings-site-name" placeholder="AmanitaLove.ru">
            <label>Описание сайта</label>
            <textarea id="settings-site-desc" rows="2" placeholder="Интернет-магазин сушеных грибов премиум-класса"></textarea>
            <label>Ключевые слова</label>
            <input type="text" id="settings-site-keywords" placeholder="сушеные грибы, купить грибы">

            <h3>Контакты</h3>
            <label>Телефон</label>
            <input type="text" id="settings-phone" placeholder="+7 (800) 555-19-87">
            <label>Email</label>
            <input type="email" id="settings-email" placeholder="hello@amanitalove.ru">
            <label>Адрес</label>
            <input type="text" id="settings-address" placeholder="г. Москва, ул. Лесная, д. 15">
            <label>Часы работы</label>
            <input type="text" id="settings-hours" placeholder="Пн–Вс: 9:00–21:00">

            <h3>Социальные сети</h3>
            <label>Telegram</label>
            <input type="text" id="settings-telegram" placeholder="https://t.me/...">
            <label>VK</label>
            <input type="text" id="settings-vk" placeholder="https://vk.com/...">
            <label>YouTube</label>
            <input type="text" id="settings-youtube" placeholder="https://youtube.com/...">
            <label>WhatsApp</label>
            <input type="text" id="settings-whatsapp" placeholder="https://wa.me/...">

            <h3>Логотип и фавиконка</h3>
            <label>Логотип (заменит текст в шапке)</label>
            <input type="file" id="settings-logo-file" accept="image/*">
            <input type="hidden" id="settings-logo-url">
            <button type="button" id="clear-logo-btn" class="btn btn-outline btn-sm" style="margin-top: 0.5rem;">🗑 Удалить логотип</button>

            <label style="margin-top: 1rem;">Фавиконка (иконка сайта)</label>
            <input type="file" id="settings-favicon-file" accept="image/*">
            <input type="hidden" id="settings-favicon-url">

            <h3>Главная страница</h3>
            <label>Показывать блок «Преимущества»</label>
            <select id="settings-show-advantages">
                <option value="yes">Да</option>
                <option value="no">Нет</option>
            </select>

            <h3>Аналитика</h3>
            <label>Google Analytics (GA4 ID)</label>
            <input type="text" id="settings-ga-id" placeholder="G-XXXXXXXXXX">
            <label>Яндекс.Метрика (ID счётчика)</label>
            <input type="text" id="settings-ym-id" placeholder="12345678">
            <label>Google Search Console (мета-тег)</label>
            <input type="text" id="settings-gsc-tag" placeholder="содержимое тега">
            <label>Яндекс.Вебмастер (мета-тег)</label>
            <input type="text" id="settings-ywm-tag" placeholder="содержимое тега">

            <h3>Служебное</h3>
            <label>robots.txt</label>
            <textarea id="settings-robots" rows="6" placeholder="User-agent: *\nDisallow: /admin/"></textarea>

            <button type="submit" class="btn btn-primary">💾 Сохранить настройки</button>
        </form>
    </div>
</div>

<!-- Модальное окно удаления -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal">
        <p>Удалить эту запись?</p>
        <button class="btn btn-danger" id="confirm-delete">🗑 Удалить</button>
        <button class="btn btn-outline" id="cancel-delete">Отмена</button>
    </div>
</div>

<script src="/js/jquery-3.6.0.min.js"></script>
<script src="/js/summernote/summernote-lite.js"></script>
<script src="/js/summernote/lang/summernote-ru-RU.js"></script>
<script src="admin.js"></script>
<script>
    async function applyTheme() {
        try {
            const res = await fetch('/api/settings');
            const settings = await res.json();
            document.documentElement.setAttribute('data-theme', settings.theme || 'dark');
        } catch(e) { }
    }
    applyTheme();
</script>
</body>
</html>