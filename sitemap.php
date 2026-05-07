<?php
header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://amanitalove.ru';
$dataDir = __DIR__ . '/data/';

$urls = [];

$urls[] = ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'];
$urls[] = ['loc' => $baseUrl . '/catalog', 'changefreq' => 'daily', 'priority' => '0.9'];
$urls[] = ['loc' => $baseUrl . '/blog', 'changefreq' => 'daily', 'priority' => '0.8'];
$urls[] = ['loc' => $baseUrl . '/news', 'changefreq' => 'daily', 'priority' => '0.8'];
$urls[] = ['loc' => $baseUrl . '/contacts', 'changefreq' => 'monthly', 'priority' => '0.5'];

$products = json_decode(file_get_contents($dataDir . 'products.json'), true);
if (is_array($products)) {
    foreach ($products as $product) {
        $slug = $product['slug'] ?? 'product-' . $product['id'];
        $urls[] = ['loc' => $baseUrl . '/product/' . $slug . '.html', 'changefreq' => 'weekly', 'priority' => '0.8'];
    }
}

$blog = json_decode(file_get_contents($dataDir . 'blog.json'), true);
if (is_array($blog)) {
    foreach ($blog as $article) {
        $slug = $article['slug'] ?? 'blog-' . $article['id'];
        $urls[] = ['loc' => $baseUrl . '/blog/' . $slug . '.html', 'changefreq' => 'monthly', 'priority' => '0.7'];
    }
}

$news = json_decode(file_get_contents($dataDir . 'news.json'), true);
if (is_array($news)) {
    foreach ($news as $item) {
        $slug = $item['slug'] ?? 'news-' . $item['id'];
        $urls[] = ['loc' => $baseUrl . '/news/' . $slug . '.html', 'changefreq' => 'monthly', 'priority' => '0.6'];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url): ?>
    <url>
        <loc><?php echo htmlspecialchars($url['loc']); ?></loc>
        <changefreq><?php echo $url['changefreq']; ?></changefreq>
        <priority><?php echo $url['priority']; ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>