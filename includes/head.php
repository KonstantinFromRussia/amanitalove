<?php
$settings = json_decode(file_get_contents(__DIR__ . '/../data/settings.json'), true);
$page_title = $page_title ?? ($settings['site_name'] ?? 'Магазин');
$page_description = $page_description ?? ($settings['site_description'] ?? 'Мухоморы и не только');
$page_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="ru" data-theme="<?php echo $settings['theme'] ?? 'dark'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Метатеги верификации -->
    <?php if (!empty($settings['google_verification'])): ?>
    <meta name="google-site-verification" content="<?php echo htmlspecialchars($settings['google_verification']); ?>">
    <?php endif; ?>
    <?php if (!empty($settings['yandex_verification'])): ?>
    <meta name="yandex-verification" content="<?php echo htmlspecialchars($settings['yandex_verification']); ?>">
    <?php endif; ?>
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($page_url); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($settings['site_name'] ?? 'AmanitaLove'); ?>">
    <?php if (!empty($settings['logo_url'])): ?>
    <meta property="og:image" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo ltrim($settings['logo_url'], '/'); ?>">
    <?php endif; ?>
    
    <!-- CSS -->
    <link rel="stylesheet" href="tailwind-output.css">
    <link rel="stylesheet" href="themes.css">
    <link rel="stylesheet" href="styles.css">
    
    <!-- Google Analytics -->
    <?php if (!empty($settings['ga_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($settings['ga_id']); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($settings['ga_id']); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Яндекс.Метрика -->
    <?php if (!empty($settings['ya_counter_id'])): ?>
    <script>
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        ym(<?php echo (int)$settings['ya_counter_id']; ?>, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/<?php echo (int)$settings['ya_counter_id']; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <?php endif; ?>
    
    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo htmlspecialchars($settings['site_name'] ?? 'AmanitaLove'); ?>",
        "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>",
        "description": "<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>"
    }
    </script>
</head>
<body>