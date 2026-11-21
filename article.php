<?php
require_once __DIR__.'/config/bootstrap.php';
$slug=trim((string)($_GET['slug']??''));
$stmt=$pdo->prepare("SELECT * FROM articles WHERE slug=:slug AND status='published' LIMIT 1");
$stmt->execute(['slug'=>$slug]);$article=$stmt->fetch();
if(!$article) redirect(app_url('insight.php'));
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($article['title'])?> — JanatulAulia Studio</title><meta name="description" content="<?=e(mb_strimwidth($article['excerpt']??'',0,155,'…'))?>"><link rel="stylesheet" href="assets/css/site.css?v=article-20261120"><link rel="stylesheet" href="assets/css/bright-theme.css?v=article-20261120"></head><body class="jas-site jas-article-page">
<header class="jas-header jas-header-light"><nav class="jas-nav"><a href="index.php" class="jas-brand"><span class="jas-brand-mark">J<span>.</span></span><span class="jas-brand-copy"><strong>JANATULAULIA</strong><small>CREATIVE STUDIO</small></span></a><a href="insight.php" class="jas-nav-cta">All insights <span>↗</span></a></nav></header>
<main><article class="jas-article"><header class="jas-wrap"><a class="jas-detail-back" href="insight.php">← Back to insights</a><div class="jas-detail-meta"><span><?=date('d M Y',strtotime($article['published_at']))?></span><?php if(!empty($article['tags'])):?><span><?=e($article['tags'])?></span><?php endif;?></div><h1><?=e($article['title'])?></h1><?php if(!empty($article['excerpt'])):?><p><?=e($article['excerpt'])?></p><?php endif;?></header><?php if(!empty($article['cover_image'])):?><div class="jas-detail-cover jas-wrap"><img src="<?=e($article['cover_image'])?>" alt="<?=e($article['title'])?>"></div><?php endif;?><div class="jas-article-content jas-wrap"><?=nl2br(e($article['content']))?></div></article>
<section class="jas-detail-cta"><div class="jas-wrap"><h2>Need a creative partner<br><em>for your next idea?</em></h2><a href="index.php#brief" class="jas-button jas-button-lime">Start a conversation <span>↗</span></a></div></section></main>
<footer class="jas-footer"><div class="jas-wrap jas-footer-top"><a href="index.php" class="jas-footer-brand">JanatulAulia<span>.</span></a><p>Creative ideas, made visible.</p></div></footer><?php require __DIR__.'/partials/chat_widget.php';?></body></html>
