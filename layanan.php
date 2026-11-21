<?php
require_once __DIR__ . '/config/bootstrap.php';

$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
    redirect(app_url('index.php#services'));
}

// Load service by slug
$stmtSvc = $pdo->prepare(
    "SELECT id, name, slug, description, deliverables, duration_label
     FROM services
     WHERE slug = :slug AND status = 'published'
     LIMIT 1"
);
$stmtSvc->execute(['slug' => $slug]);
$service = $stmtSvc->fetch();

if (!$service) {
    redirect(app_url('index.php#services'));
}

// Load published portfolio works in matching category (slug match)
$stmtWork = $pdo->prepare(
    "SELECT p.id, p.title, p.slug, p.summary, p.year, p.cover_image, c.name AS category, c.slug AS category_slug
     FROM projects p
     INNER JOIN project_categories c ON c.id = p.category_id
     WHERE p.status = 'published' AND c.slug = :cslug
     ORDER BY p.published_at DESC, p.id DESC"
);
$stmtWork->execute(['cslug' => $service['slug']]);
$works = $stmtWork->fetchAll();

// Load all published services for "other services" section
$stmtOther = $pdo->prepare(
    "SELECT id, name, slug, description, duration_label
     FROM services
     WHERE status = 'published' AND slug != :slug
     ORDER BY id ASC LIMIT 4"
);
$stmtOther->execute(['slug' => $slug]);
$otherServices = $stmtOther->fetchAll();

// Deliverables as bullet list
$deliverableLines = array_filter(array_map('trim', explode("\n", $service['deliverables'] ?? '')));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($service['name']) ?> — JanatulAulia Studio</title>
  <meta name="description" content="<?= e(mb_strimwidth($service['description'], 0, 155, '…')) ?>">
  <meta name="theme-color" content="#F4EFE6">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/site.css?v=service-layout-20261120-02">
  <link rel="stylesheet" href="assets/css/bright-theme.css?v=service-layout-20261120-02">
</head>
<body class="jas-site jas-layanan-page">

<!-- Header -->
<header class="jas-header jas-header-light">
  <nav class="jas-nav">
    <a href="index.php" class="jas-brand" aria-label="JanatulAulia Studio home">
      <span class="jas-brand-mark">J<span>.</span></span>
      <span class="jas-brand-copy"><strong>JANATULAULIA</strong><small>CREATIVE STUDIO</small></span>
    </a>
    <div class="jas-menu" id="jas-menu">
      <a href="index.php#about">Studio</a>
      <a href="index.php#services">Services</a>
      <a href="portfolio.php">Portfolio</a>
      <a href="index.php#brief" class="jas-nav-cta">Start a project <span>↗</span></a>
    </div>
    <button class="jas-menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="jas-menu"><span></span><span></span></button>
  </nav>
</header>

<!-- Hero Section -->
<section class="jas-layanan-hero">
  <div class="jas-wrap">
    <nav class="jas-breadcrumb" aria-label="Breadcrumb">
      <a href="index.php">Home</a>
      <span>→</span>
      <a href="index.php#services">Services</a>
      <span>→</span>
      <span><?= e($service['name']) ?></span>
    </nav>
    <div class="jas-layanan-hero-inner">
      <div class="jas-layanan-hero-copy">
        <div class="jas-section-index jas-index-light">
          <span>SERVICES</span><i></i><small>JANATULAULIA STUDIO</small>
        </div>
        <h1><?= e($service['name']) ?></h1>
        <p><?= e($service['description']) ?></p>
        <div class="jas-layanan-cta-row">
          <a href="index.php#brief" class="jas-button jas-button-lime">Start this project <span>↗</span></a>
          <a href="index.php#services" class="jas-text-link jas-text-link-light">← All services</a>
        </div>
      </div>
      <aside class="jas-layanan-info-box">
        <?php if ($service['duration_label']): ?>
        <div class="jas-info-row">
          <span>Estimated timeline</span>
          <b><?= e($service['duration_label']) ?></b>
        </div>
        <?php endif; ?>
        <div class="jas-info-row">
          <span>Category</span>
          <b><?= e($service['name']) ?></b>
        </div>
        <div class="jas-info-row">
          <span>Portfolio</span>
          <b><?= count($works) ?> projects</b>
        </div>
        <?php if ($deliverableLines): ?>
        <div class="jas-info-deliverables">
          <span>What you'll get</span>
          <ul>
            <?php foreach ($deliverableLines as $line): ?>
              <li><?= e($line) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>

<!-- Portfolio Works Section -->
<main class="jas-layanan-works-wrap jas-wrap">
  <div class="jas-layanan-works-head">
    <div>
      <div class="jas-section-index">
        <span>PORTFOLIO</span><i></i><small><?= e(strtoupper($service['name'])) ?></small>
      </div>
      <h2>Explore the work<br><em>behind this service.</em></h2>
      <p>Selected projects offer a glimpse into JanatulAulia Studio's approach to <?= e($service['name']) ?>.</p>
    </div>
    <?php if ($works): ?>
    <a href="portfolio.php?kategori=<?= urlencode($service['slug']) ?>" class="jas-outline-link">
      Browse the portfolio <span>↗</span>
    </a>
    <?php endif; ?>
  </div>

  <?php if ($works): ?>
  <div class="jas-layanan-work-grid">
    <?php foreach ($works as $i => $work): ?>
    <article class="jas-layanan-work-card <?= $i === 0 ? 'jas-layanan-work-featured' : '' ?>">
      <a href="portfolio.php?kategori=<?= urlencode($work['category_slug']) ?>" class="jas-layanan-work-img" tabindex="-1" aria-hidden="true">
        <?php if ($work['cover_image']): ?>
          <img src="<?= e($work['cover_image']) ?>" alt="<?= e($work['title']) ?>" loading="lazy">
        <?php else: ?>
          <div class="jas-layanan-work-placeholder">
            <span><?= e(mb_substr($work['title'], 0, 2)) ?></span>
          </div>
        <?php endif; ?>
        <span class="jas-layanan-work-arrow">↗</span>
      </a>
      <div class="jas-layanan-work-info">
        <div>
          <span><?= e($work['category']) ?> · <?= e($work['year']) ?></span>
          <h3><?= e($work['title']) ?></h3>
          <?php if ($work['summary']): ?>
            <p><?= e($work['summary']) ?></p>
          <?php endif; ?>
        </div>
        <b>0<?= $i + 1 ?></b>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="jas-layanan-empty">
    <span>COMING SOON</span>
    <h2>Work in progress.</h2>
    <p>There are no published projects for <strong><?= e($service['name']) ?></strong> yet. New work is on the way.</p>
    <a href="index.php#brief" class="jas-button jas-button-dark">Start a project with us <span>↗</span></a>
  </div>
  <?php endif; ?>
</main>

<!-- Other Services Section -->
<?php if ($otherServices): ?>
<section class="jas-layanan-other">
  <div class="jas-wrap">
    <div class="jas-layanan-other-head">
      <span>MORE SERVICES</span>
      <h2>Explore more<br>from our studio.</h2>
    </div>
    <div class="jas-layanan-other-grid">
      <?php foreach ($otherServices as $i => $svc): ?>
      <a href="layanan.php?slug=<?= urlencode($svc['slug']) ?>" class="jas-layanan-other-card">
        <div class="jas-layanan-other-top">
          <span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <b>↗</b>
        </div>
        <h3><?= e($svc['name']) ?></h3>
        <p><?= e(mb_strimwidth($svc['description'], 0, 90, '…')) ?></p>
        <div class="jas-layanan-other-foot">
          <span><?= e($svc['duration_label'] ?: 'Flexible timeline') ?></span>
          <span>Explore →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA Strip -->
<section class="jas-layanan-cta-strip">
  <div class="jas-wrap jas-layanan-cta-inner">
    <div>
      <h2>Have a project for<br><em>this service?</em></h2>
      <p>Tell us about your idea and what you need. We'll be in touch to discuss the next steps.</p>
    </div>
    <a href="index.php#brief" class="jas-button jas-button-lime jas-button-lg">
      Tell us about your project <span>↗</span>
    </a>
  </div>
</section>

<!-- Footer -->
<footer class="jas-footer">
  <div class="jas-wrap jas-footer-top">
    <a href="index.php" class="jas-footer-brand">JanatulAulia<span>.</span></a>
    <p>Small studio. Big ideas.</p>
    <a href="auth/login.php" class="jas-footer-admin">Admin portal ↗</a>
  </div>
  <div class="jas-wrap jas-footer-bottom">
    <span>© <?= date('Y') ?> JanatulAulia Studio. All rights reserved.</span>
    <span>Made with intention in Indonesia.</span>
  </div>
</footer>

<?php require __DIR__ . '/partials/chat_widget.php'; ?>
<script>
(() => {
  const toggle = document.querySelector('.jas-menu-toggle');
  const menu = document.querySelector('#jas-menu');
  if (!toggle || !menu) return;
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!open));
    menu.classList.toggle('is-open', !open);
  });
  menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    toggle.setAttribute('aria-expanded', 'false');
    menu.classList.remove('is-open');
  }));
})();
</script>
</body>
</html>
