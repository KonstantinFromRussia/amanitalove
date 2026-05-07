(function() {
    window.products = [];
    window.blogArticles = [];
    window.newsItems = [];

    let cart = JSON.parse(localStorage.getItem('amanitalove_cart')) || [];

    // ========== ГЛОБАЛЬНЫЕ ФУНКЦИИ ==========
    window.toggleCart = function() {
        const s = document.getElementById('cartSidebar');
        const o = document.getElementById('cartOverlay');
        s.classList.toggle('open');
        o.classList.toggle('open');
        document.body.style.overflow = s.classList.contains('open') ? 'hidden' : '';
    };

    window.toggleMobileMenu = function() {
        const m = document.getElementById('mobileMenu');
        const o = document.getElementById('mobileMenuOverlay');
        m.classList.toggle('open');
        o.classList.toggle('open');
        document.body.style.overflow = m.classList.contains('open') ? 'hidden' : '';
    };

    window.closeOrderModal = function() {
        document.getElementById('order-modal-overlay').classList.remove('open');
        document.getElementById('order-modal').classList.remove('open');
        document.body.style.overflow = '';
    };

    window.checkout = function() {
        if (!cart.length) return;
        document.getElementById('order-modal-overlay').classList.add('open');
        document.getElementById('order-modal').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.clearCart = function() {
        if (!cart.length) return;
        cart = [];
        saveCart();
        showToast('🧹 Корзина очищена');
        document.getElementById('cartSidebar').classList.remove('open');
        document.getElementById('cartOverlay').classList.remove('open');
    };

    window.addToCart = function(id) {
        const p = window.products.find(x => x.id === id);
        if (!p) return;
        const exist = findInCart(id);
        exist ? exist.qty++ : cart.push({...p, qty:1});
        saveCart();
        showToast(`✅ "${p.name}" добавлен в корзину`);
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');
        if (!sidebar.classList.contains('open')) {
            sidebar.classList.add('open');
            overlay.classList.add('open');
            setTimeout(() => {
                if (sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                }
            }, 1500);
        }
    };

    window.changeQty = function(id, d) {
        const item = findInCart(id);
        if (!item) return;
        item.qty += d;
        if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
        saveCart();
    };

    window.removeFromCart = function(id) {
        const item = findInCart(id);
        if (item) {
            cart = cart.filter(i => i.id !== id);
            saveCart();
            showToast(`🗑 "${item.name}" удалён`);
        }
    };

    window.submitOrder = async function() {
        const name = document.getElementById('order-name').value.trim();
        const phone = document.getElementById('order-phone').value.trim();
        const email = document.getElementById('order-email').value.trim();
        const address = document.getElementById('order-address').value.trim();
        const comment = document.getElementById('order-comment').value.trim();

        if (!name || !phone) {
            showToast('⚠️ Заполните имя и телефон');
            return;
        }

        const order = {
            customer: { name, phone, email, address, comment },
            items: cart.map(i => ({ id: i.id, name: i.name, price: i.price, qty: i.qty })),
            total: getCartTotal()
        };

        try {
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(order)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Ошибка');
            showToast('✅ Заказ успешно оформлен!');
            cart = [];
            saveCart();
            window.closeOrderModal();
            document.getElementById('cartSidebar').classList.remove('open');
            document.getElementById('cartOverlay').classList.remove('open');
            document.body.style.overflow = '';
        } catch (e) {
            showToast('❌ Не удалось оформить заказ');
            console.error(e);
        }
    };

    // ========== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ==========
    function saveCart() {
        localStorage.setItem('amanitalove_cart', JSON.stringify(cart));
        updateCartUI();
    }
    function getCartTotal() { return cart.reduce((sum, i) => sum + i.price * i.qty, 0); }
    function getCartCount() { return cart.reduce((sum, i) => sum + i.qty, 0); }
    function findInCart(id) { return cart.find(i => i.id === id); }

    function updateCartUI() {
        const count = getCartCount();
        const badge = document.getElementById('cartBadge');
        const mobileCount = document.getElementById('mobileCartCount');
        const cartFooter = document.getElementById('cartFooter');
        const container = document.getElementById('cartItemsContainer');
        const totalEl = document.getElementById('cartTotal');

        if (count > 0) {
            badge.style.display = 'flex';
            badge.textContent = count;
            badge.classList.add('bump');
            setTimeout(() => badge.classList.remove('bump'), 300);
        } else {
            badge.style.display = 'none';
        }
        if (mobileCount) mobileCount.textContent = count > 0 ? `(${count})` : '';

        if (cart.length === 0) {
            container.innerHTML = '<p class="text-[var(--text-muted)] text-center mt-12">Ваша корзина пока пуста 🍄</p>';
            cartFooter.style.display = 'none';
        } else {
            cartFooter.style.display = 'block';
            container.innerHTML = cart.map(item => `
                <div class="flex items-center gap-3 py-3 border-b border-[var(--border)]">
                    <div class="w-12 h-12 rounded-[var(--radius-sm)] flex items-center justify-center text-2xl shrink-0"
                         style="background:${item.gradient || '#1a1a1a'};">
                        ${item.image ? `<img src="${item.image}" alt="${item.alt || item.name}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-sm)">` : item.emoji}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[var(--text-primary)] truncate">${item.name}</p>
                        <p class="text-xs text-[var(--text-muted)]">${item.price} ₽ × ${item.qty}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-primary)] w-7 h-7 rounded-full cursor-pointer text-sm flex items-center justify-center hover:border-[var(--accent)]"
                                onclick="window.changeQty(${item.id}, -1)">−</button>
                        <span class="text-sm w-6 text-center text-[var(--text-primary)]">${item.qty}</span>
                        <button class="bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-primary)] w-7 h-7 rounded-full cursor-pointer text-sm flex items-center justify-center hover:border-[var(--accent)]"
                                onclick="window.changeQty(${item.id}, 1)">+</button>
                    </div>
                    <button class="text-[var(--text-muted)] hover:text-[var(--danger)] bg-transparent border-none cursor-pointer text-lg"
                            onclick="window.removeFromCart(${item.id})" title="Удалить">🗑</button>
                </div>
            `).join('');
            totalEl.textContent = getCartTotal().toLocaleString('ru-RU') + ' ₽';
        }
    }

    function showToast(msg) {
        const container = document.getElementById('toastContainer');
        const div = document.createElement('div');
        div.className = 'toast';
        div.textContent = msg;
        container.appendChild(div);
        setTimeout(() => div.remove(), 2600);
    }

    // ========== РЕНДЕРИНГ КАРТОЧЕК (с ALT) ==========
    function createProductCard(p) {
        const slug = p.slug || 'product-' + p.id;
        const inCart = findInCart(p.id);
        const altText = p.alt || p.name || 'Изображение товара';
        const imageHtml = p.image
            ? `<img src="${p.image}" alt="${altText}" style="width:100%;height:100%;object-fit:cover;">`
            : `<span class="product-card__image-placeholder" style="font-size:5rem;">${p.emoji}</span>`;
        return `
            <div class="product-card" data-category="${p.category}">
                <a href="/product/${slug}.html" class="product-card__image" style="background:${p.gradient}; display:block;">
                    ${imageHtml}
                </a>
                <div class="p-4 flex flex-col flex-1 gap-2">
                    <span class="text-xs text-[var(--accent)] font-medium uppercase tracking-wide">${p.latin}</span>
                    <a href="/product/${slug}.html" class="no-underline"><h3 class="font-semibold text-[var(--text-primary)] leading-tight">${p.name}</h3></a>
                    <p class="text-xs text-[var(--text-secondary)] flex-1">${p.desc}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-lg font-bold text-[var(--accent)]">${p.price.toLocaleString('ru-RU')} ₽</span>
                        <button class="btn-primary btn-sm" onclick="event.preventDefault(); event.stopPropagation(); window.addToCart(${p.id});">
                            🛒 В корзину ${inCart ? `<span style="font-size:0.7rem;opacity:0.8;">(${inCart.qty})</span>` : ''}
                        </button>
                    </div>
                </div>
            </div>`;
    }

    function createBlogCard(a) {
        const slug = a.slug || 'blog-' + a.id;
        const altText = a.alt || a.title || 'Изображение статьи';
        const thumbStyle = a.image ? `background-image:url('${a.image}');background-size:cover;` : `background:${a.gradient};`;
        const thumbContent = a.image ? `<img src="${a.image}" alt="${altText}" style="width:100%;height:100%;object-fit:cover;">` : `<span>${a.emoji}</span>`;
        return `
            <div class="article-card">
                <a href="/blog/${slug}.html" class="article-card__thumb" style="${thumbStyle}">${thumbContent}</a>
                <div class="p-5 flex flex-col flex-1 gap-2">
                    <span class="text-xs text-[var(--text-muted)]">${a.date}</span>
                    <a href="/blog/${slug}.html" class="no-underline"><h3 class="font-semibold text-[var(--text-primary)] leading-snug">${a.title}</h3></a>
                    <p class="text-sm text-[var(--text-secondary)] flex-1">${a.excerpt}</p>
                    <a href="/blog/${slug}.html" class="text-[var(--accent)] text-sm font-medium hover:underline">Читать дальше →</a>
                </div>
            </div>`;
    }

    function createNewsCard(n) {
        const slug = n.slug || 'news-' + n.id;
        return `
            <div class="article-card p-5 flex flex-col gap-3">
                <span class="text-xs text-[var(--text-muted)]">${n.date}</span>
                <a href="/news/${slug}.html" class="no-underline"><h3 class="font-semibold text-[var(--text-primary)] leading-snug">${n.title}</h3></a>
                <p class="text-sm text-[var(--text-secondary)] flex-1">${n.excerpt}</p>
                <a href="/news/${slug}.html" class="text-[var(--accent)] text-sm font-medium hover:underline">Подробнее →</a>
            </div>`;
    }

    // Рендер с сортировкой
    function renderFeaturedProducts() {
        const g = document.getElementById('featuredGrid');
        if (g && window.products.length) {
            const sorted = [...window.products].reverse();
            g.innerHTML = sorted.slice(0,4).map(createProductCard).join('');
        }
    }
    function renderCatalogGrid(filter='all') {
        const g = document.getElementById('catalogGrid');
        if (!g || !window.products.length) return;
        const filtered = filter==='all' ? window.products : window.products.filter(p=>p.category===filter);
        const sorted = [...filtered].reverse();
        g.innerHTML = sorted.map(createProductCard).join('');
        g.querySelectorAll('.product-card').forEach((c,i)=>{
            c.style.opacity='0'; c.style.transform='translateY(20px)';
            c.style.transition=`opacity 0.4s ease ${i*0.05}s, transform 0.4s ease ${i*0.05}s`;
            requestAnimationFrame(()=>{c.style.opacity='1'; c.style.transform='translateY(0)';});
        });
    }
    function renderBlogPreview() {
        const g = document.getElementById('blogPreviewGrid');
        if (g && window.blogArticles.length) {
            const sorted = [...window.blogArticles].reverse();
            g.innerHTML = sorted.slice(0,3).map(createBlogCard).join('');
        }
    }
    function renderBlogFull() {
        const g = document.getElementById('blogFullGrid');
        if (g && window.blogArticles.length) {
            const sorted = [...window.blogArticles].reverse();
            g.innerHTML = sorted.map(createBlogCard).join('');
        }
    }
    function renderNewsPreview() {
        const g = document.getElementById('newsPreviewGrid');
        if (g && window.newsItems.length) {
            const sorted = [...window.newsItems].reverse();
            g.innerHTML = sorted.slice(0,3).map(createNewsCard).join('');
        }
    }
    function renderNewsFull() {
        const g = document.getElementById('newsFullGrid');
        if (g && window.newsItems.length) {
            const sorted = [...window.newsItems].reverse();
            g.innerHTML = sorted.map(createNewsCard).join('');
        }
    }

    function initCatalogFilters() {
        const cont = document.getElementById('catalogFilters');
        if (!cont) return;
        cont.addEventListener('click', e => {
            const btn = e.target.closest('.filter-btn');
            if (!btn) return;
            cont.querySelectorAll('.filter-btn').forEach(b=>{b.classList.remove('btn-primary','active'); b.classList.add('btn-outline');});
            btn.classList.remove('btn-outline'); btn.classList.add('btn-primary','active');
            renderCatalogGrid(btn.dataset.filter);
        });
    }

    function initContactForm() {
        const form = document.getElementById('contactForm');
        if (!form) return;
        form.addEventListener('submit', e => {
            e.preventDefault();
            const name = document.getElementById('contactName').value.trim();
            const email = document.getElementById('contactEmail').value.trim();
            const msg = document.getElementById('contactMessage').value.trim();
            if (!name || !email || !msg) { showToast('⚠️ Заполните все поля'); return; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('⚠️ Некорректный email'); return; }
            document.getElementById('contactSuccess').classList.remove('hidden');
            showToast('✅ Сообщение отправлено!');
            form.reset();
            setTimeout(() => document.getElementById('contactSuccess').classList.add('hidden'), 5000);
        });
    }

    function initScrollAnimations() {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.fade-in-section').forEach(el => obs.observe(el));
    }
    function initBackToTop() {
        const btn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            btn.style.display = window.scrollY > 600 ? 'flex' : 'none';
        });
    }
    function initHeaderShadow() {
        const h = document.getElementById('siteHeader');
        window.addEventListener('scroll', () => h.classList.toggle('scrolled', window.scrollY > 20));
    }

    async function loadData() {
        try {
            const [prodRes, blogRes, newsRes] = await Promise.all([
                fetch('/api/products'),
                fetch('/api/blog'),
                fetch('/api/news')
            ]);
            if (!prodRes.ok || !blogRes.ok || !newsRes.ok) throw new Error('Ошибка загрузки');
            window.products = await prodRes.json();
            window.blogArticles = await blogRes.json();
            window.newsItems = await newsRes.json();
        } catch(e) {
            console.error('Ошибка загрузки данных:', e);
            showToast('⚠️ Не удалось загрузить каталог');
        }
    }

    function determinePageType() {
        const path = window.location.pathname;
        if (path === '/' || path === '/index.html') return 'home';
        if (path.startsWith('/catalog')) return 'catalog';
        if (path.startsWith('/blog') && !path.includes('/blog/')) return 'blog';
        if (path.startsWith('/news') && !path.includes('/news/')) return 'news';
        if (path.startsWith('/contacts')) return 'contacts';
        return 'other';
    }

    async function init() {
        await loadData();

        const pageType = determinePageType();

        if (pageType === 'home') {
            renderFeaturedProducts();
            renderBlogPreview();
            renderNewsPreview();
        } else if (pageType === 'catalog') {
            renderCatalogGrid('all');
        } else if (pageType === 'blog') {
            renderBlogFull();
        } else if (pageType === 'news') {
            renderNewsFull();
        }

        initCatalogFilters();
        initContactForm();
        initScrollAnimations();
        initBackToTop();
        initHeaderShadow();
        updateCartUI();

        document.getElementById('cartOverlay').addEventListener('click', window.toggleCart);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (document.getElementById('order-modal-overlay').classList.contains('open')) window.closeOrderModal();
                if (document.getElementById('cartSidebar').classList.contains('open')) window.toggleCart();
                if (document.getElementById('mobileMenu').classList.contains('open')) window.toggleMobileMenu();
            }
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();