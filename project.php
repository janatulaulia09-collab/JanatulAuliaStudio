<?php
require_once __DIR__ . '/config/bootstrap.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$stmt = $pdo->prepare("SELECT p.*, c.name AS category, c.slug AS category_slug FROM projects p INNER JOIN project_categories c ON c.id=p.category_id WHERE p.slug=:slug AND p.status='published' LIMIT 1");
$stmt->execute(['slug'=>$slug]);
$project = $stmt->fetch();
if (!$project) redirect(app_url('portfolio.php'));
$media = $pdo->prepare('SELECT file_path, alt_text FROM project_media WHERE project_id=:id ORDER BY sort_order, id');
$media->execute(['id'=>$project['id']]);
$mediaItems = $media->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($project['title'])?> — JanatulAulia Studio</title><meta name="description" content="<?=e(mb_strimwidth($project['summary'],0,155,'…'))?>"><link rel="stylesheet" href="assets/css/site.css?v=detail-20261120"><link rel="stylesheet" href="assets/css/bright-theme.css?v=detail-20261120"></head><body class="jas-site jas-detail-page">
<header class="jas-header jas-header-light"><nav class="jas-nav"><a href="index.php" class="jas-brand"><span class="jas-brand-mark">J<span>.</span></span><span class="jas-brand-copy"><strong>JANATULAULIA</strong><small>CREATIVE STUDIO</small></span></a><a href="portfolio.php" class="jas-nav-cta">All projects <span>↗</span></a></nav></header>
<main><section class="jas-detail-hero"><div class="jas-wrap"><a class="jas-detail-back" href="portfolio.php?kategori=<?=urlencode($project['category_slug'])?>">← Back to <?=e($project['category'])?></a><div class="jas-detail-meta"><span><?=e($project['category'])?></span><span><?=e($project['year'])?></span></div><h1><?=e($project['title'])?></h1><p><?=e($project['summary'])?></p></div></section>
<section class="jas-detail-cover jas-wrap"><img src="<?=e($project['cover_image'])?>" alt="<?=e($project['title'])?>"></section>
<section class="jas-detail-content jas-wrap"><aside><span>CASE STUDY</span><b><?=e($project['year'])?></b></aside><article><h2>Project overview</h2><div><?=nl2br(e($project['description']))?></div></article></section>
<?php if ($mediaItems): ?><section class="jas-detail-gallery jas-wrap"><?php foreach($mediaItems as $item): ?><figure><img src="<?=e($item['file_path'])?>" alt="<?=e($item['alt_text']?:$project['title'])?>" loading="lazy"></figure><?php endforeach; ?></section><?php endif; ?>
<section class="jas-detail-cta"><div class="jas-wrap"><h2>Have a similar<br><em>challenge?</em></h2><a href="index.php#brief" class="jas-button jas-button-lime">Start a conversation <span>↗</span></a></div></section></main>
<footer class="jas-footer"><div class="jas-wrap jas-footer-top"><a href="index.php" class="jas-footer-brand">JanatulAulia<span>.</span></a><p>Small studio. Big ideas.</p><a href="index.php" class="jas-footer-admin">Back to home ↗</a></div></footer>
<?php require __DIR__.'/partials/chat_widget.php'; ?>
</body></html>
