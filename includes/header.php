<?php
$settings = json_decode(file_get_contents(__DIR__ . '/../data/settings.json'), true);
$site_name = $settings['site_name'] ?? 'AmanitaLove';
$logo_url = !empty($settings['logo_url']) ? $settings['logo_url'] : null;
?>
<header class="sticky top-0 z-50 bg-background/95 backdrop-blur-md border-b border-border shadow-sm">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <div class="logo-container flex-1 min-w-0">
            <?php if ($logo_url): ?>
                <img src="<?php echo htmlspecialchars($logo_url); ?>" 
                     alt="<?php echo htmlspecialchars($site_name); ?>" 
                     class="logo-image w-full h-auto object-contain max-h-16 md:max-h-20"
                     style="width: auto; max-width: 100%;">
            <?php else: ?>
                <a href="/" class="text-2xl font-bold text-primary hover:opacity-90 transition">
                    <?php echo htmlspecialchars($site_name); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <nav class="hidden md:flex items-center gap-6">
            <a href="/" class="hover:text-primary transition">Главная</a>
            <a href="/catalog" class="hover:text-primary transition">Каталог</a>
            <a href="/blog" class="hover:text-primary transition">Блог</a>
            <a href="/news" class="hover:text-primary transition">Новости</a>
            <a href="/contacts" class="hover:text-primary transition">Контакты</a>
        </nav>
        
        <div class="flex items-center gap-3">
            <button class="cart-btn relative p-2 rounded-full hover:bg-muted transition" onclick="toggleCart()" aria-label="Корзина">
                🛒
                <span id="cartBadge" class="absolute -top-1 -right-1 bg-primary text-primary-foreground text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display:none;">0</span>
            </button>
            <button id="theme-toggle" class="p-2 rounded-full hover:bg-muted transition" onclick="toggleTheme()">🌙</button>
        </div>
    </div>
</header>