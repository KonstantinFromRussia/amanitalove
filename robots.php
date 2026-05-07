<?php
header('Content-Type: text/plain; charset=utf-8');
$settings = json_decode(file_get_contents(__DIR__ . '/data/settings.json'), true);
echo $settings['robots'] ?? "User-agent: *\nDisallow: /admin/\nDisallow: /api/\nDisallow: /data/\nSitemap: https://amanitalove.ru/sitemap.xml";