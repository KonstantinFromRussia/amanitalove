document.addEventListener('DOMContentLoaded', function() {
    let products = [];
    let blog = [];
    let news = [];
    let orders = [];
    let settings = {
        theme: 'dark',
        site_name: '', site_description: '', site_keywords: '',
        phone: '', email: '', address: '', hours: '',
        telegram: '', vk: '', youtube: '', whatsapp: '',
        logo_url: '', favicon_url: '',
        show_advantages: 'yes',
        ga_id: '', ym_id: '', gsc_tag: '', ywm_tag: '',
        robots: ''
    };

    const statusDiv = document.getElementById('status');
    const tabs = document.querySelectorAll('.tab');
    const panels = document.querySelectorAll('.form-panel');
    const deleteModal = document.getElementById('delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    let deleteContext = { type: null, id: null };
    let imageUploading = false;

    // ========== ТРАНСЛИТЕРАЦИЯ ==========
    function generateSlug(text) {
        const map = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
            'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i',
            'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
            'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
            'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch',
            'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '',
            'э': 'e', 'ю': 'yu', 'я': 'ya',
            'А': 'a', 'Б': 'b', 'В': 'v', 'Г': 'g', 'Д': 'd',
            'Е': 'e', 'Ё': 'yo', 'Ж': 'zh', 'З': 'z', 'И': 'i',
            'Й': 'y', 'К': 'k', 'Л': 'l', 'М': 'm', 'Н': 'n',
            'О': 'o', 'П': 'p', 'Р': 'r', 'С': 's', 'Т': 't',
            'У': 'u', 'Ф': 'f', 'Х': 'h', 'Ц': 'ts', 'Ч': 'ch',
            'Ш': 'sh', 'Щ': 'sch', 'Ъ': '', 'Ы': 'y', 'Ь': '',
            'Э': 'e', 'Ю': 'yu', 'Я': 'ya'
        };
        let slug = '';
        for (let char of text) {
            slug += map[char] || char;
        }
        slug = slug.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        return slug || 'untitled';
    }

    document.getElementById('prod-name').addEventListener('input', function() {
        document.getElementById('prod-slug').value = generateSlug(this.value);
    });
    document.getElementById('blog-title').addEventListener('input', function() {
        document.getElementById('blog-slug').value = generateSlug(this.value);
    });
    document.getElementById('news-title').addEventListener('input', function() {
        document.getElementById('news-slug').value = generateSlug(this.value);
    });

    // ========== ЗАГРУЗКА ИЗОБРАЖЕНИЙ ==========
    async function uploadImage(file, urlFieldId) {
        if (!file) return;
        imageUploading = true;
        showStatus('⏳ Загрузка изображения...', false);
        const formData = new FormData();
        formData.append('image', file);
        try {
            const res = await fetch('/upload.php', { method: 'POST', body: formData, credentials: 'same-origin' });
            if (!res.ok) throw new Error(await res.text());
            const url = await res.text();
            document.getElementById(urlFieldId).value = url;
            showStatus('✅ Изображение загружено', false);
        } catch (e) {
            console.error(e);
            alert('Не удалось загрузить изображение');
            showStatus('❌ Ошибка загрузки изображения', true);
        } finally {
            imageUploading = false;
        }
    }

    async function uploadFavicon(file) {
        if (!file) return;
        imageUploading = true;
        showStatus('⏳ Загрузка фавиконки...', false);
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'favicon');
        try {
            const res = await fetch('/upload.php', { method: 'POST', body: formData, credentials: 'same-origin' });
            if (!res.ok) throw new Error(await res.text());
            const url = await res.text();
            document.getElementById('settings-favicon-url').value = url;
            showStatus('✅ Фавиконка загружена', false);
        } catch (e) {
            console.error(e);
            alert('Не удалось загрузить фавиконку');
            showStatus('❌ Ошибка загрузки', true);
        } finally {
            imageUploading = false;
        }
    }

    document.getElementById('prod-image-file').addEventListener('change', function() {
        if (this.files[0]) uploadImage(this.files[0], 'prod-image-url');
    });
    document.getElementById('blog-image-file').addEventListener('change', function() {
        if (this.files[0]) uploadImage(this.files[0], 'blog-image-url');
    });
    document.getElementById('settings-logo-file').addEventListener('change', function() {
        if (this.files[0]) uploadImage(this.files[0], 'settings-logo-url');
    });
    document.getElementById('settings-favicon-file').addEventListener('change', function() {
        if (this.files[0]) uploadFavicon(this.files[0]);
    });

    // Кнопка удаления логотипа
    const clearLogoBtn = document.getElementById('clear-logo-btn');
    if (clearLogoBtn) {
    clearLogoBtn.addEventListener('click', async () => {
    document.getElementById('settings-logo-url').value = '';
    document.getElementById('settings-logo-file').value = '';
    showStatus('Логотип удалён, сохраняю...', false);
    // Автоматически сохраняем настройки
    const form = document.getElementById('settings-form');
    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
    form.dispatchEvent(submitEvent);
});
    }

    function isImageUploading() {
        if (imageUploading) { alert('Дождитесь окончания загрузки изображения'); return true; }
        return false;
    }

    // ========== SUMMERNOTE ==========
    if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
        function handleImageUpload(files, editor) {
            var data = new FormData();
            data.append('image', files[0]);
            $.ajax({
                url: '/upload.php', method: 'POST', data: data,
                processData: false, contentType: false,
                success: function(url) { $(editor).summernote('insertImage', url); },
                error: function() { alert('Ошибка загрузки изображения'); }
            });
        }
        $('#prod-details, #blog-content, #news-content').each(function() {
            var $editor = $(this);
            $editor.summernote({
                height: 250, lang: 'ru-RU',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['codeview', 'help']],
                ],
                callbacks: { onImageUpload: function(files) { handleImageUpload(files, $editor); } }
            });
        });
    }

    // ========== УТИЛИТЫ ==========
    function showStatus(msg, isError = false) {
        statusDiv.textContent = msg;
        statusDiv.className = 'status-msg ' + (isError ? 'status-error' : 'status-success');
        statusDiv.style.display = 'block';
        setTimeout(() => { statusDiv.style.display = 'none'; }, 3000);
    }

    function getNextId(arr) { return arr.length ? Math.max(...arr.map(i => i.id)) + 1 : 1; }

    // ========== ЗАГРУЗКА ДАННЫХ ==========
    async function loadData() {
        try {
            const [pRes, bRes, nRes, oRes, sRes] = await Promise.all([
                fetch('/api/products'), fetch('/api/blog'), fetch('/api/news'), fetch('/api/orders'), fetch('/api/settings')
            ]);
            products = await pRes.json();
            blog = await bRes.json();
            news = await nRes.json();
            orders = await oRes.json();
            settings = await sRes.json();

            document.getElementById('settings-theme').value = settings.theme || 'dark';
            document.getElementById('settings-site-name').value = settings.site_name || '';
            document.getElementById('settings-site-desc').value = settings.site_description || '';
            document.getElementById('settings-site-keywords').value = settings.site_keywords || '';
            document.getElementById('settings-phone').value = settings.phone || '';
            document.getElementById('settings-email').value = settings.email || '';
            document.getElementById('settings-address').value = settings.address || '';
            document.getElementById('settings-hours').value = settings.hours || '';
            document.getElementById('settings-telegram').value = settings.telegram || '';
            document.getElementById('settings-vk').value = settings.vk || '';
            document.getElementById('settings-youtube').value = settings.youtube || '';
            document.getElementById('settings-whatsapp').value = settings.whatsapp || '';
            document.getElementById('settings-logo-url').value = settings.logo_url || '';
            document.getElementById('settings-favicon-url').value = settings.favicon_url || '';
            document.getElementById('settings-show-advantages').value = settings.show_advantages || 'yes';
            document.getElementById('settings-ga-id').value = settings.ga_id || '';
            document.getElementById('settings-ym-id').value = settings.ym_id || '';
            document.getElementById('settings-gsc-tag').value = settings.gsc_tag || '';
            document.getElementById('settings-ywm-tag').value = settings.ywm_tag || '';
            document.getElementById('settings-robots').value = settings.robots || '';

            document.documentElement.setAttribute('data-theme', settings.theme || 'dark');
            renderAllLists();
            renderOrdersList();
        } catch (e) { showStatus('Ошибка загрузки данных', true); }
    }

    async function saveData(type, arr) {
        try {
            const res = await fetch('/api/' + type, {
                method: 'PUT', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(arr), credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Ошибка сохранения');
            showStatus('✅ Сохранено');
            await loadData();
        } catch (e) { showStatus('❌ Ошибка сохранения', true); }
    }

    // ========== РЕНДЕРИНГ СПИСКОВ ==========
    function renderList(containerId, items, type) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const sorted = [...items].reverse();
        container.innerHTML = sorted.map(item => {
            let title = '', sub = '';
            if (type === 'product') {
                title = (item.emoji || '') + ' ' + item.name;
                sub = `${item.price} ₽ | ${item.category}`;
            } else {
                title = item.title;
                sub = item.date || '';
            }
            return `
                <div class="list-item">
                    <div class="info"><div class="title">${title}</div><div class="sub">${sub}</div></div>
                    <div class="actions">
                        <button class="btn btn-outline btn-sm edit-btn" data-type="${type}" data-id="${item.id}">✏️</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-type="${type}" data-id="${item.id}">🗑</button>
                    </div>
                </div>`;
        }).join('');

        container.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.type;
                const id = parseInt(btn.dataset.id);
                const arr = type === 'product' ? products : type === 'blog' ? blog : news;
                const item = arr.find(i => i.id === id);
                if (item) {
                    fillForm(type, item);
                    document.getElementById('panel-' + type).scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        container.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                deleteContext.type = btn.dataset.type;
                deleteContext.id = parseInt(btn.dataset.id);
                deleteModal.classList.add('active');
            });
        });
    }

    function renderOrdersList() {
        const container = document.getElementById('orders-list');
        if (!container) return;
        if (!orders.length) { container.innerHTML = '<p>Нет заказов</p>'; return; }
        container.innerHTML = orders.map(order => `
            <div class="list-item">
                <div class="info">
                    <div class="title">Заказ #${order.id} от ${order.date}</div>
                    <div class="sub">${order.customer.name} | ${order.customer.phone} | Сумма: ${order.total}₽</div>
                    <div class="sub">Статус:
                        <select class="order-status" data-id="${order.id}">
                            <option ${order.status === 'Новый' ? 'selected' : ''}>Новый</option>
                            <option ${order.status === 'В обработке' ? 'selected' : ''}>В обработке</option>
                            <option ${order.status === 'Отправлен' ? 'selected' : ''}>Отправлен</option>
                            <option ${order.status === 'Выполнен' ? 'selected' : ''}>Выполнен</option>
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn btn-danger btn-sm" onclick="deleteOrder(${order.id})">🗑</button>
                </div>
            </div>
        `).join('');
    }

    document.addEventListener('change', async function(e) {
        if (e.target.classList.contains('order-status')) {
            const id = parseInt(e.target.dataset.id);
            const order = orders.find(o => o.id === id);
            if (order) {
                order.status = e.target.value;
                await fetch('/api/orders', {
                    method: 'PUT', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orders), credentials: 'same-origin'
                });
                showStatus('Статус обновлён');
            }
        }
    });

    window.deleteOrder = async function(id) {
        if (!confirm('Удалить заказ?')) return;
        orders = orders.filter(o => o.id !== id);
        await fetch('/api/orders', {
            method: 'PUT', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orders), credentials: 'same-origin'
        });
        renderOrdersList();
        showStatus('Заказ удалён');
    };

    function renderAllLists() {
        renderList('products-list', products, 'product');
        renderList('blog-list', blog, 'blog');
        renderList('news-list', news, 'news');
    }

    // ========== ФОРМЫ ==========
    window.clearForm = function(type) {
        if (type === 'product') {
            document.getElementById('prod-id').value = '';
            document.getElementById('prod-name').value = '';
            document.getElementById('prod-slug').value = '';
            document.getElementById('prod-latin').value = '';
            document.getElementById('prod-category').value = 'culinary';
            document.getElementById('prod-price').value = '';
            document.getElementById('prod-image-file').value = '';
            document.getElementById('prod-image-url').value = '';
            document.getElementById('prod-alt').value = '';
            document.getElementById('prod-emoji').value = '';
            document.getElementById('prod-desc').value = '';
            document.getElementById('prod-gradient').value = '';
            document.getElementById('prod-keywords').value = '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#prod-details').summernote('code', '');
            } else {
                document.getElementById('prod-details').value = '';
            }
        } else if (type === 'blog') {
            document.getElementById('blog-id').value = '';
            document.getElementById('blog-title').value = '';
            document.getElementById('blog-slug').value = '';
            document.getElementById('blog-excerpt').value = '';
            document.getElementById('blog-image-file').value = '';
            document.getElementById('blog-image-url').value = '';
            document.getElementById('blog-alt').value = '';
            document.getElementById('blog-emoji').value = '';
            document.getElementById('blog-date').value = '';
            document.getElementById('blog-gradient').value = '';
            document.getElementById('blog-keywords').value = '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#blog-content').summernote('code', '');
            } else {
                document.getElementById('blog-content').value = '';
            }
        } else if (type === 'news') {
            document.getElementById('news-id').value = '';
            document.getElementById('news-title').value = '';
            document.getElementById('news-slug').value = '';
            document.getElementById('news-excerpt').value = '';
            document.getElementById('news-date').value = '';
            document.getElementById('news-keywords').value = '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#news-content').summernote('code', '');
            } else {
                document.getElementById('news-content').value = '';
            }
        }
    };

    function fillForm(type, item) {
        if (type === 'product') {
            document.getElementById('prod-id').value = item.id;
            document.getElementById('prod-name').value = item.name || '';
            document.getElementById('prod-slug').value = item.slug || '';
            document.getElementById('prod-latin').value = item.latin || '';
            document.getElementById('prod-category').value = item.category || 'culinary';
            document.getElementById('prod-price').value = item.price || '';
            document.getElementById('prod-image-url').value = item.image || '';
            document.getElementById('prod-alt').value = item.alt || '';
            document.getElementById('prod-emoji').value = item.emoji || '';
            document.getElementById('prod-desc').value = item.desc || '';
            document.getElementById('prod-gradient').value = item.gradient || '';
            document.getElementById('prod-keywords').value = item.keywords || '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#prod-details').summernote('code', item.details || '');
            } else {
                document.getElementById('prod-details').value = item.details || '';
            }
        } else if (type === 'blog') {
            document.getElementById('blog-id').value = item.id;
            document.getElementById('blog-title').value = item.title || '';
            document.getElementById('blog-slug').value = item.slug || '';
            document.getElementById('blog-excerpt').value = item.excerpt || '';
            document.getElementById('blog-image-url').value = item.image || '';
            document.getElementById('blog-alt').value = item.alt || '';
            document.getElementById('blog-emoji').value = item.emoji || '';
            document.getElementById('blog-date').value = item.date || '';
            document.getElementById('blog-gradient').value = item.gradient || '';
            document.getElementById('blog-keywords').value = item.keywords || '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#blog-content').summernote('code', item.content || '');
            } else {
                document.getElementById('blog-content').value = item.content || '';
            }
        } else if (type === 'news') {
            document.getElementById('news-id').value = item.id;
            document.getElementById('news-title').value = item.title || '';
            document.getElementById('news-slug').value = item.slug || '';
            document.getElementById('news-excerpt').value = item.excerpt || '';
            document.getElementById('news-date').value = item.date || '';
            document.getElementById('news-keywords').value = item.keywords || '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.summernote) {
                $('#news-content').summernote('code', item.content || '');
            } else {
                document.getElementById('news-content').value = item.content || '';
            }
        }
    }

    // ========== СОХРАНЕНИЕ ФОРМ ==========
    document.getElementById('product-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isImageUploading()) return;
        const id = document.getElementById('prod-id').value;
        const details = (typeof jQuery !== 'undefined' && jQuery.fn.summernote)
            ? $('#prod-details').summernote('code')
            : document.getElementById('prod-details').value;
        const item = {
            id: id ? parseInt(id) : getNextId(products),
            name: document.getElementById('prod-name').value,
            slug: document.getElementById('prod-slug').value,
            latin: document.getElementById('prod-latin').value,
            category: document.getElementById('prod-category').value,
            price: parseInt(document.getElementById('prod-price').value) || 0,
            image: document.getElementById('prod-image-url').value,
            alt: document.getElementById('prod-alt').value,
            emoji: document.getElementById('prod-emoji').value,
            desc: document.getElementById('prod-desc').value,
            gradient: document.getElementById('prod-gradient').value,
            details: details,
            keywords: document.getElementById('prod-keywords').value
        };
        const newArr = id ? products.map(p => p.id === item.id ? item : p) : [...products, item];
        await saveData('products', newArr);
        clearForm('product');
    });

    document.getElementById('blog-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isImageUploading()) return;
        const id = document.getElementById('blog-id').value;
        const content = (typeof jQuery !== 'undefined' && jQuery.fn.summernote)
            ? $('#blog-content').summernote('code')
            : document.getElementById('blog-content').value;
        const item = {
            id: id ? parseInt(id) : getNextId(blog),
            title: document.getElementById('blog-title').value,
            slug: document.getElementById('blog-slug').value,
            excerpt: document.getElementById('blog-excerpt').value,
            image: document.getElementById('blog-image-url').value,
            alt: document.getElementById('blog-alt').value,
            emoji: document.getElementById('blog-emoji').value,
            date: document.getElementById('blog-date').value,
            gradient: document.getElementById('blog-gradient').value,
            content: content,
            keywords: document.getElementById('blog-keywords').value
        };
        const newArr = id ? blog.map(b => b.id === item.id ? item : b) : [...blog, item];
        await saveData('blog', newArr);
        clearForm('blog');
    });

    document.getElementById('news-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (isImageUploading()) return;
        const id = document.getElementById('news-id').value;
        const content = (typeof jQuery !== 'undefined' && jQuery.fn.summernote)
            ? $('#news-content').summernote('code')
            : document.getElementById('news-content').value;
        const item = {
            id: id ? parseInt(id) : getNextId(news),
            title: document.getElementById('news-title').value,
            slug: document.getElementById('news-slug').value,
            excerpt: document.getElementById('news-excerpt').value,
            date: document.getElementById('news-date').value,
            content: content,
            keywords: document.getElementById('news-keywords').value
        };
        const newArr = id ? news.map(n => n.id === item.id ? item : n) : [...news, item];
        await saveData('news', newArr);
        clearForm('news');
    });

    // ========== НАСТРОЙКИ ==========
    document.getElementById('settings-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        settings.theme = document.getElementById('settings-theme').value;
        settings.site_name = document.getElementById('settings-site-name').value;
        settings.site_description = document.getElementById('settings-site-desc').value;
        settings.site_keywords = document.getElementById('settings-site-keywords').value;
        settings.phone = document.getElementById('settings-phone').value;
        settings.email = document.getElementById('settings-email').value;
        settings.address = document.getElementById('settings-address').value;
        settings.hours = document.getElementById('settings-hours').value;
        settings.telegram = document.getElementById('settings-telegram').value;
        settings.vk = document.getElementById('settings-vk').value;
        settings.youtube = document.getElementById('settings-youtube').value;
        settings.whatsapp = document.getElementById('settings-whatsapp').value;
        settings.logo_url = document.getElementById('settings-logo-url').value;
        settings.favicon_url = document.getElementById('settings-favicon-url').value;
        settings.show_advantages = document.getElementById('settings-show-advantages').value;
        settings.ga_id = document.getElementById('settings-ga-id').value;
        settings.ym_id = document.getElementById('settings-ym-id').value;
        settings.gsc_tag = document.getElementById('settings-gsc-tag').value;
        settings.ywm_tag = document.getElementById('settings-ywm-tag').value;
        settings.robots = document.getElementById('settings-robots').value;

        try {
            const res = await fetch('/api/settings', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings),
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Ошибка сохранения настроек');
            showStatus('✅ Настройки сохранены');
            document.documentElement.setAttribute('data-theme', settings.theme);
        } catch (e) {
            showStatus('❌ Ошибка сохранения настроек', true);
        }
    });

    // ========== МОДАЛЬНОЕ УДАЛЕНИЕ ==========
    confirmDeleteBtn.addEventListener('click', async () => {
        const { type, id } = deleteContext;
        let arr;
        if (type === 'product') arr = products.filter(p => p.id !== id);
        else if (type === 'blog') arr = blog.filter(b => b.id !== id);
        else if (type === 'news') arr = news.filter(n => n.id !== id);
        await saveData(type, arr);
        deleteModal.classList.remove('active');
    });
    cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.remove('active'));
    deleteModal.addEventListener('click', (e) => { if (e.target === deleteModal) deleteModal.classList.remove('active'); });

    // ========== ВКЛАДКИ ==========
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('panel-' + tabName).classList.add('active');
        });
    });

    loadData();
});