<?php
/**
 * Single Article Page with Full SEO
 */
require_once __DIR__ . '/../includes/functions.php';

// Get article by slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    redirect('/blog');
}

$stmt = db()->prepare("
    SELECT a.*, c.name_ar as category_name, c.slug as category_slug,
           u.name as author_name, u.email as author_email
    FROM articles a
    LEFT JOIN article_categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// Increment views
db()->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);

// Get related articles
$relatedArticles = db()->prepare("
    SELECT id, title, slug, featured_image, published_at, reading_time
    FROM articles
    WHERE status = 'published' AND id != ? AND category_id = ?
    ORDER BY published_at DESC
    LIMIT 4
");
$relatedArticles->execute([$article['id'], $article['category_id']]);
$relatedArticles = $relatedArticles->fetchAll();

// If no related in same category, get recent
if (count($relatedArticles) < 4) {
    $moreArticles = db()->prepare("
        SELECT id, title, slug, featured_image, published_at, reading_time
        FROM articles
        WHERE status = 'published' AND id != ?
        ORDER BY published_at DESC
        LIMIT ?
    ");
    $moreArticles->execute([$article['id'], 4 - count($relatedArticles)]);
    $relatedArticles = array_merge($relatedArticles, $moreArticles->fetchAll());
}

// Get FAQs if any
$faqs = db()->prepare("SELECT * FROM faqs WHERE entity_type = 'article' AND entity_id = ? AND is_active = 1 ORDER BY sort_order");
$faqs->execute([$article['id']]);
$faqs = $faqs->fetchAll();

// SEO
$seoTitle = $article['meta_title'] ?: $article['title'];
$seoDescription = $article['meta_description'] ?: $article['excerpt'] ?: mb_substr(strip_tags($article['content']), 0, 160);
$seoKeywords = $article['meta_keywords'] ?: '';
$ogImage = $article['og_image'] ?: $article['featured_image'];
$canonicalUrl = $article['canonical_url'] ?: '';
$robots = $article['robots'] ?: 'index, follow';

// Schema Markup
$siteUrl = getSetting('site_url', 'https://example.com');
$schemaArticle = [
    '@context' => 'https://schema.org',
    '@type' => $article['schema_type'] ?: 'Article',
    'headline' => $article['title'],
    'description' => $seoDescription,
    'image' => $article['featured_image'] ? $siteUrl . '/uploads/articles/' . $article['featured_image'] : '',
    'datePublished' => date('c', strtotime($article['published_at'])),
    'dateModified' => date('c', strtotime($article['updated_at'])),
    'author' => [
        '@type' => 'Person',
        'name' => $article['author_name'] ?: 'فريق التحرير'
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => getSetting('site_name', 'بي تاج'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $siteUrl . '/images/logo.png'
        ]
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $siteUrl . '/blog/' . $article['slug']
    ]
];

// FAQ Schema
$schemaFaq = null;
if (!empty($faqs)) {
    $schemaFaq = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(function($faq) {
            return [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }, $faqs)
    ];
}

// BreadcrumbList Schema
$schemaBreadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => $siteUrl],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'المدونة', 'item' => $siteUrl . '/blog'],
    ]
];
if ($article['category_name']) {
    $schemaBreadcrumb['itemListElement'][] = [
        '@type' => 'ListItem', 'position' => 3,
        'name' => $article['category_name'],
        'item' => $siteUrl . '/blog?category=' . $article['category_slug']
    ];
    $schemaBreadcrumb['itemListElement'][] = [
        '@type' => 'ListItem', 'position' => 4, 'name' => $article['title']
    ];
} else {
    $schemaBreadcrumb['itemListElement'][] = [
        '@type' => 'ListItem', 'position' => 3, 'name' => $article['title']
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title><?= htmlspecialchars($seoTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
    <?php if ($seoKeywords): ?>
    <meta name="keywords" content="<?= htmlspecialchars($seoKeywords) ?>">
    <?php endif; ?>
    <meta name="robots" content="<?= $robots ?>">
    <?php if ($canonicalUrl): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php else: ?>
    <link rel="canonical" href="<?= $siteUrl ?>/blog/<?= $article['slug'] ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= htmlspecialchars($article['og_title'] ?: $seoTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['og_description'] ?: $seoDescription) ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?= $siteUrl ?>/uploads/articles/<?= $ogImage ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?= $siteUrl ?>/blog/<?= $article['slug'] ?>">
    <meta property="og:site_name" content="<?= getSetting('site_name', 'بي تاج') ?>">
    <meta property="article:published_time" content="<?= date('c', strtotime($article['published_at'])) ?>">
    <meta property="article:modified_time" content="<?= date('c', strtotime($article['updated_at'])) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($article['twitter_title'] ?: $seoTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($article['twitter_description'] ?: $seoDescription) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image" content="<?= $siteUrl ?>/uploads/articles/<?= $ogImage ?>">
    <?php endif; ?>

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json"><?= json_encode($schemaArticle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
    <script type="application/ld+json"><?= json_encode($schemaBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
    <?php if ($schemaFaq): ?>
    <script type="application/ld+json"><?= json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
    <?php endif; ?>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">

    <style>
        body { font-family: 'Cairo', sans-serif; }
        .article-content {
            font-size: 1.1rem;
            line-height: 2;
        }
        .article-content h2, .article-content h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1e3a8a;
        }
        .article-content p { margin-bottom: 1.5rem; }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        .article-content ul, .article-content ol {
            margin-bottom: 1.5rem;
            padding-right: 2rem;
        }
        .article-content li { margin-bottom: 0.5rem; }
        .share-buttons a {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #fff;
            text-decoration: none;
            transition: transform 0.2s;
        }
        .share-buttons a:hover { transform: scale(1.1); }
        .toc { position: sticky; top: 100px; }
        .toc a { color: #666; text-decoration: none; display: block; padding: 5px 0; border-right: 2px solid #eee; padding-right: 15px; }
        .toc a:hover, .toc a.active { color: #1e3a8a; border-color: #1e3a8a; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Hero -->
<div class="bg-dark text-white position-relative" style="min-height:400px">
    <?php if ($article['featured_image']): ?>
    <img src="/uploads/articles/<?= $article['featured_image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>"
         class="position-absolute w-100 h-100" style="object-fit:cover;opacity:0.3">
    <?php endif; ?>
    <div class="container position-relative py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($article['category_name']): ?>
                <a href="/blog?category=<?= $article['category_slug'] ?>" class="badge bg-primary text-decoration-none mb-3">
                    <?= htmlspecialchars($article['category_name']) ?>
                </a>
                <?php endif; ?>

                <h1 class="display-4 fw-bold mb-4"><?= htmlspecialchars($article['title']) ?></h1>

                <div class="d-flex flex-wrap align-items-center gap-4">
                    <?php if ($article['author_name']): ?>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                             style="width:40px;height:40px">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?= $article['author_name'] ?></div>
                            <small class="opacity-75">الكاتب</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div>
                        <i class="far fa-calendar me-1"></i>
                        <?= formatDate($article['published_at']) ?>
                    </div>
                    <div>
                        <i class="far fa-clock me-1"></i>
                        <?= $article['reading_time'] ?> دقائق للقراءة
                    </div>
                    <div>
                        <i class="far fa-eye me-1"></i>
                        <?= number_format($article['views']) ?> مشاهدة
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="bg-light py-2">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="/blog">المدونة</a></li>
            <?php if ($article['category_name']): ?>
            <li class="breadcrumb-item"><a href="/blog?category=<?= $article['category_slug'] ?>"><?= $article['category_name'] ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= mb_substr($article['title'], 0, 30) ?>...</li>
        </ol>
    </div>
</nav>

<div class="container py-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Excerpt -->
            <?php if ($article['excerpt']): ?>
            <div class="lead bg-light p-4 rounded-3 mb-4">
                <?= htmlspecialchars($article['excerpt']) ?>
            </div>
            <?php endif; ?>

            <!-- Article Content -->
            <article class="article-content">
                <?= $article['content'] ?>
            </article>

            <!-- FAQs -->
            <?php if (!empty($faqs)): ?>
            <div class="mt-5">
                <h2 class="h3 mb-4"><i class="fas fa-question-circle me-2"></i>الأسئلة الشائعة</h2>
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $i => $faq): ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                                <?= htmlspecialchars($faq['question']) ?>
                            </button>
                        </h3>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?= $faq['answer'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Share -->
            <div class="border-top border-bottom py-4 my-4">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>شارك المقال:</strong>
                    <div class="share-buttons d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($siteUrl . '/blog/' . $article['slug']) ?>"
                           target="_blank" style="background:#1877f2" title="مشاركة على فيسبوك">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($siteUrl . '/blog/' . $article['slug']) ?>&text=<?= urlencode($article['title']) ?>"
                           target="_blank" style="background:#1da1f2" title="مشاركة على تويتر">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' ' . $siteUrl . '/blog/' . $article['slug']) ?>"
                           target="_blank" style="background:#25d366" title="مشاركة على واتساب">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($siteUrl . '/blog/' . $article['slug']) ?>"
                           target="_blank" style="background:#0077b5" title="مشاركة على لينكد إن">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <button onclick="navigator.clipboard.writeText('<?= $siteUrl ?>/blog/<?= $article['slug'] ?>')"
                                class="btn btn-outline-secondary btn-sm" title="نسخ الرابط">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
            <div class="mt-5">
                <h2 class="h4 mb-4">مقالات ذات صلة</h2>
                <div class="row g-4">
                    <?php foreach ($relatedArticles as $related): ?>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if ($related['featured_image']): ?>
                            <img src="/uploads/articles/<?= $related['featured_image'] ?>" class="card-img-top"
                                 alt="<?= htmlspecialchars($related['title']) ?>" style="height:150px;object-fit:cover">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="h6">
                                    <a href="/blog/<?= $related['slug'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($related['title']) ?>
                                    </a>
                                </h3>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i><?= $related['reading_time'] ?> دقائق
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top:100px">
                <!-- Table of Contents -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>محتويات المقال</h5>
                    </div>
                    <div class="card-body toc" id="toc">
                        <!-- Auto-generated by JS -->
                    </div>
                </div>

                <!-- CTA -->
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-home fa-3x mb-3"></i>
                        <h5>هل تبحث عن عقار؟</h5>
                        <p class="small opacity-75">تصفح أحدث العقارات المتاحة للبيع والإيجار</p>
                        <a href="/search" class="btn btn-light">تصفح العقارات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// Generate Table of Contents
document.addEventListener('DOMContentLoaded', function() {
    const article = document.querySelector('.article-content');
    const toc = document.getElementById('toc');
    const headings = article.querySelectorAll('h2, h3');

    if (headings.length > 0) {
        headings.forEach((heading, index) => {
            heading.id = 'section-' + index;
            const link = document.createElement('a');
            link.href = '#section-' + index;
            link.textContent = heading.textContent;
            link.style.paddingRight = heading.tagName === 'H3' ? '30px' : '15px';
            toc.appendChild(link);
        });
    } else {
        toc.innerHTML = '<p class="text-muted small">لا توجد عناوين فرعية</p>';
    }
});
</script>

</body>
</html>
