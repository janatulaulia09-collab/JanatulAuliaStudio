<?php
require_once __DIR__ . '/config/bootstrap.php';

$services = $pdo->query("SELECT id, name, slug, description, deliverables, duration_label FROM services WHERE status = 'published' ORDER BY id DESC LIMIT 6")->fetchAll();
$portfolio = $pdo->query("SELECT p.id, p.title, p.slug, p.summary, p.year, p.cover_image, c.name AS category FROM projects p INNER JOIN project_categories c ON c.id = p.category_id WHERE p.status = 'published' ORDER BY p.published_at DESC, p.id DESC LIMIT 3")->fetchAll();
$articles = $pdo->query("SELECT id, title, slug, excerpt, cover_image, published_at FROM articles WHERE status = 'published' ORDER BY published_at DESC, id DESC LIMIT 3")->fetchAll();
$briefSuccess = isset($_GET['brief']) && $_GET['brief'] === 'success';
$briefError = isset($_GET['brief']) && $_GET['brief'] === 'error';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JanatulAulia Studio — Design that feels like you</title>
  <meta name="description" content="Thoughtful brands and digital experiences by JanatulAulia Studio.">
  <meta name="theme-color" content="#F4EFE6">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/site.css?v=figma-20261121-01"><link rel="stylesheet" href="assets/css/bright-theme.css?v=figma-20261121-01">
</head>
<body class="jas-site">
<header class="jas-header">
  <nav class="jas-nav" aria-label="Main navigation">
    <a href="index.php" class="jas-brand" aria-label="JanatulAulia Studio home">
      <span class="jas-brand-mark">J<span>.</span></span>
      <span class="jas-brand-copy"><strong>JANATULAULIA</strong><small>CREATIVE STUDIO</small></span>
    </a>
    <button class="jas-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="jas-menu"><span></span><span></span></button>
    <div class="jas-menu" id="jas-menu">
      <a href="#about">Studio</a><a href="#services">Services</a><a href="#work">Portfolio</a><a href="insight.php">Insights</a>
      <a class="jas-nav-cta" href="#brief">Start a project <span>↗</span></a>
    </div>
  </nav>
</header>
<main>
  <section class="jas-hero">
    <div class="jas-hero-noise" aria-hidden="true"></div>
    <div class="jas-hero-orbit jas-orbit-one" aria-hidden="true"></div><div class="jas-hero-orbit jas-orbit-two" aria-hidden="true"></div>
    <div class="jas-hero-inner">
      <div class="jas-hero-copy">
        <p class="jas-kicker jas-kicker-light"><span></span> Creative studio</p>
        <h1>Design that<br>feels like <em>you.</em></h1>
        <p class="jas-hero-lead">Thoughtful brands and digital experiences.</p>
        <div class="jas-hero-actions"><a href="#work" class="jas-button jas-button-lime">Explore our work <span>↗</span></a><a href="#brief" class="jas-text-link jas-text-link-light">Start a project <span>↓</span></a></div>
        <div class="jas-hero-meta"><span>BRANDING</span><i></i><span>DIGITAL</span><i></i><span>CREATIVE DIRECTION</span></div>
      </div>
      <div class="jas-hero-art" aria-label="Creative studio visual collage">
        <div class="jas-art-backdrop"></div>
        <div class="jas-art-photo jas-art-photo-main"><img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=88" alt="Creative studio workspace" fetchpriority="high"></div>
        <div class="jas-art-photo jas-art-photo-small"><img src="https://images.unsplash.com/photo-1545235617-9465d2a55698?auto=format&fit=crop&w=700&q=85" alt="Visual design exploration"></div>
        <div class="jas-art-sticker"><span>MAKE IT</span><strong>matter.</strong><small>CREATIVE WITH PURPOSE</small></div>
        <div class="jas-art-caption"><span>01 / 04</span><span>THOUGHTFUL BY DESIGN</span></div>
      </div>
    </div>
    <div class="jas-hero-bottom"><span>SMALL STUDIO. BIG IDEAS.</span><a href="#about">Scroll to explore <b>↓</b></a></div>
  </section>

  <section class="jas-intro jas-wrap" id="about">
    <div class="jas-section-index"><span>01</span><i></i><small>ABOUT THE STUDIO</small></div>
    <div class="jas-intro-main">
      <h2>Small studio.<br><em>Big ideas.</em></h2>
      <div class="jas-intro-side"><p>We partner with people and growing brands to shape thoughtful identities and digital experiences. Strategy, creativity, and design come together to make every idea feel unmistakably yours.</p><a href="#services" class="jas-round-link" aria-label="Explore services">↘</a></div>
    </div>
    <div class="jas-values"><div><b>01</b><span>Think with intention</span></div><div><b>02</b><span>Design with feeling</span></div><div><b>03</b><span>Make meaningful impact</span></div></div>
  </section>

  <section class="jas-services" id="services">
    <div class="jas-wrap">
      <div class="jas-section-head"><div><div class="jas-section-index"><span>02</span><i></i><small>WHAT WE DO</small></div><h2>Thoughtful work<br>for <em>growing ideas.</em></h2></div><p>From finding a brand's character to creating consistent content, we make sure every detail has a purpose.</p></div>
      <div class="jas-service-grid">
        <?php foreach ($services as $i => $service): ?>
          <a href="layanan.php?slug=<?= urlencode((string)($service['slug'] ?? slugify((string)$service['name']))) ?>" class="jas-service-card" aria-label="Explore <?= e($service['name']) ?>"><div class="jas-service-top"><span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><b>↗</b></div><h3><?= e($service['name']) ?></h3><p><?= e($service['description']) ?></p><div class="jas-service-foot"><span><?= e($service['duration_label'] ?: 'Flexible timeline') ?></span><span class="jas-service-explore">Explore service →</span></div></a>
        <?php endforeach; ?>
        <?php if (!$services): ?><div class="jas-empty-state"><span>✳</span><h3>Room for the next big idea.</h3><p>Services are being prepared. Check back soon to see what's new.</p></div><?php endif; ?>
      </div>
    </div>
  </section>

  <section class="jas-work jas-wrap" id="work">
    <div class="jas-section-head jas-work-head"><div><div class="jas-section-index"><span>03</span><i></i><small>SELECTED WORK</small></div><h2>Ideas made visible.<br><em>Stories brought to life.</em></h2></div><a href="portfolio.php" class="jas-outline-link">Browse all projects <span>↗</span></a></div>
    <div class="jas-work-grid">
      <?php foreach ($portfolio as $i => $work): ?>
        <article class="jas-work-card <?= $i === 0 ? 'jas-work-featured' : '' ?>"><a href="project.php?slug=<?= urlencode((string)$work['slug']) ?>" class="jas-work-image"><img src="<?= e($work['cover_image']) ?>" alt="<?= e($work['title']) ?>" loading="lazy"><span class="jas-image-arrow">↗</span></a><div class="jas-work-info"><div><span><?= e($work['category']) ?> · <?= e($work['year']) ?></span><h3><a href="project.php?slug=<?= urlencode((string)$work['slug']) ?>"><?= e($work['title']) ?></a></h3><p><?= e($work['summary']) ?></p></div><b>0<?= $i + 1 ?></b></div></article>
      <?php endforeach; ?>
      <?php if (!$portfolio): ?><div class="jas-empty-work"><span>PORTFOLIO / 2026</span><p>Selected work will appear here as projects are published.</p></div><?php endif; ?>
    </div>
  </section>

  <section class="jas-insights jas-wrap" id="insights">
    <div class="jas-section-head"><div><div class="jas-section-index"><span>04</span><i></i><small>INSIGHTS &amp; NOTES</small></div><h2>Ideas, stories, and<br><em>the process behind them.</em></h2></div><a href="insight.php" class="jas-outline-link">Read all insights <span>↗</span></a></div>
    <?php if ($articles): ?>
    <div class="jas-insight-grid">
      <?php foreach ($articles as $i => $article): ?>
      <article class="jas-insight-card">
        <a href="article.php?slug=<?= urlencode((string)$article['slug']) ?>" class="jas-insight-image"><?php if (!empty($article['cover_image'])): ?><img src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy"><?php else: ?><span>INSIGHT 0<?= $i + 1 ?></span><?php endif; ?></a>
        <div><span><?= date('d M Y', strtotime((string)$article['published_at'])) ?></span><h3><a href="article.php?slug=<?= urlencode((string)$article['slug']) ?>"><?= e($article['title']) ?></a></h3><p><?= e(mb_strimwidth((string)($article['excerpt'] ?? ''), 0, 130, '…')) ?></p></div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="jas-insight-empty"><span>JOURNAL / JANATULAULIA</span><h3>New insights are on the way.</h3><p>Check back soon for studio notes and creative updates.</p></div>
    <?php endif; ?>
  </section>

  <section class="jas-statement"><div class="jas-wrap jas-statement-inner"><span class="jas-statement-mark">“</span><p>Don't just be seen.<br><em>Make it matter.</em></p><span class="jas-statement-credit">JANATULAULIA STUDIO · CREATIVE POINT OF VIEW</span></div></section>

  <section class="jas-process jas-wrap" id="process">
    <div class="jas-section-head"><div><div class="jas-section-index"><span>05</span><i></i><small>OUR PROCESS</small></div><h2>Clear steps.<br><em>Thoughtful outcomes.</em></h2></div><p>Every project has its own story and challenges. Our process keeps the path clear from the first conversation to the final delivery.</p></div>
    <div class="jas-process-grid"><?php $steps = [['01','Discover','We learn about your goals, needs, and audience.'],['02','Define','We set the scope and creative direction.'],['03','Design','We turn ideas into thoughtful visual solutions.'],['04','Deliver','We refine and hand over the finished work.']]; foreach ($steps as $step): ?><article><span><?= e($step[0]) ?></span><h3><?= e($step[1]) ?></h3><p><?= e($step[2]) ?></p></article><?php endforeach; ?></div>
  </section>

  <section class="jas-brief" id="brief">
    <div class="jas-wrap jas-brief-layout">
      <div class="jas-brief-copy"><div class="jas-section-index jas-index-light"><span>06</span><i></i><small>LET'S COLLABORATE</small></div><h2>Tell us about<br><em>your project.</em></h2><p>Share a little about your idea. We'll learn about your needs and get in touch to discuss the next steps.</p><div class="jas-brief-note"><span>LET'S MAKE SOMETHING MEANINGFUL</span><b>JanatulAulia Studio<span>.</span></b></div></div>
      <div class="jas-brief-form">
        <?php if ($briefSuccess): ?><div class="jas-alert jas-alert-success">Thank you! Your project inquiry has been sent. The JanatulAulia Studio team will review it.</div><?php elseif ($briefError): ?><div class="jas-alert jas-alert-error">Your inquiry could not be sent. Please check your details and try again.</div><?php endif; ?>
        <div class="jas-form-heading"><span>PROJECT INQUIRY / 2026</span><p>Tell us a little about the project so we can understand what you need.</p></div>
        <form method="post" action="api/submit_inquiry.php" class="jas-form-grid jas-brief-form-grid" id="project-brief-form">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="jas-honeypot"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="jas-brief-progress jas-field-wide" aria-label="Project inquiry steps">
            <span class="is-active"><b>01</b> Contact</span><i></i>
            <span><b>02</b> Project</span><i></i>
            <span><b>03</b> Details</span>
          </div>
          <p class="jas-brief-step-error jas-field-wide" id="brief-step-error" role="alert"></p>
          <div class="jas-brief-step is-active" data-step="1">
            <label class="jas-field"><span>Name <b>*</b></span><input name="name" required maxlength="120" autocomplete="name" placeholder="Your name"></label>
            <label class="jas-field"><span>Email <b>*</b></span><input type="email" name="email" required maxlength="150" autocomplete="email" placeholder="you@example.com"></label>
            <label class="jas-field"><span>Business / Brand</span><input name="company" maxlength="150" autocomplete="organization" placeholder="Your brand or business"></label>
            <label class="jas-field"><span>Project type <b>*</b></span><select name="service_id" required><option value="">Choose a service</option><?php foreach ($services as $service): ?><option value="<?= (int)$service['id'] ?>"><?= e($service['name']) ?></option><?php endforeach; ?></select></label>
            <label class="jas-field"><span>Budget range <b>*</b></span><select name="budget_range" required><option value="">Choose a range</option><option>Under IDR 500,000</option><option>IDR 500,000 – 1,000,000</option><option>IDR 1,000,000 – 3,000,000</option><option>IDR 3,000,000 – 5,000,000</option><option>Over IDR 5,000,000</option><option>Let's discuss</option></select></label>
            <label class="jas-field"><span>Timeline <b>*</b></span><input type="date" name="deadline" required min="<?= date('Y-m-d') ?>"></label>
          </div>
          <div class="jas-brief-step" data-step="2">
            <label class="jas-field"><span>Phone / WhatsApp <b>*</b></span><input name="phone" required maxlength="30" inputmode="tel" autocomplete="tel" placeholder="+62"></label>
            <label class="jas-field"><span>Project goal <b>*</b></span><input name="goal" required maxlength="500" placeholder="What would you like to achieve?"></label>
            <label class="jas-field"><span>Target audience <b>*</b></span><input name="target_audience" required maxlength="500" placeholder="Who would you like to reach?"></label>
            <label class="jas-field"><span>Reference link</span><input type="url" name="reference_url" maxlength="255" placeholder="https://example.com"></label>
            <label class="jas-field jas-field-wide"><span>Tell us about the project <b>*</b></span><textarea name="scope" required rows="5" maxlength="5000" placeholder="Share your idea, needs, audience, and what a successful outcome looks like..."></textarea></label>
          </div>
          <div class="jas-brief-step" data-step="3">
            <div class="jas-brief-review jas-field-wide">
              <h3>Review your inquiry</h3>
              <dl id="brief-review"></dl>
              <label class="jas-confirm"><input type="checkbox" name="brief_confirm" required> <span>I confirm these details are correct and may be used to follow up on my inquiry.</span></label>
            </div>
          </div>
          <div class="jas-brief-actions jas-field-wide">
            <button type="button" class="jas-brief-back" id="brief-back">← Back</button>
            <button type="button" class="jas-submit" id="brief-next">Continue <span>→</span></button>
            <button type="submit" class="jas-submit" id="brief-submit">Send inquiry <span>↗</span></button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>
<footer class="jas-footer"><div class="jas-wrap jas-footer-top"><a href="index.php" class="jas-footer-brand">JanatulAulia<span>.</span></a><p>Small studio. Big ideas.</p><a href="auth/login.php" class="jas-footer-admin">Admin portal ↗</a></div><div class="jas-wrap jas-footer-bottom"><span>© <?= date('Y') ?> JanatulAulia Studio. All rights reserved.</span><span>Made with intention in Indonesia.</span></div></footer>
<?php require __DIR__ . '/partials/chat_widget.php'; ?>
<script>
(() => {
  const briefForm = document.querySelector('#project-brief-form');
  if (briefForm) {
    let step = 1;
    const steps = [...briefForm.querySelectorAll('.jas-brief-step')];
    const progress = [...briefForm.querySelectorAll('.jas-brief-progress span')];
    const back = briefForm.querySelector('#brief-back');
    const next = briefForm.querySelector('#brief-next');
    const submit = briefForm.querySelector('#brief-submit');
    const errorText = briefForm.querySelector('#brief-step-error');
    const review = briefForm.querySelector('#brief-review');
    const customMessage = (field) => {
      if (field.name === 'email') return 'Enter a valid email address.';
      if (field.name === 'phone') return 'Enter a valid phone number (at least 8 digits).';
      if (field.name === 'reference_url') return 'Enter a complete URL starting with https:// or http://.';
      if (field.name === 'brief_confirm') return 'Confirm the details before sending your inquiry.';
      return 'Complete this field before continuing.';
    };
    const validatePanel = (panel) => {
      const fields = [...panel.querySelectorAll('input, select, textarea')];
      for (const field of fields) {
        field.value = typeof field.value === 'string' ? field.value.trim() : field.value;
        if (field.name === 'phone' && field.value) {
          const digits = field.value.replace(/\D/g, '');
          if (digits.length < 8 || digits.length > 30) {
            field.setCustomValidity('Phone number must contain 8–30 digits.');
          } else {
            field.setCustomValidity('');
          }
        }
        if (field.name === 'reference_url' && field.value && !/^https?:\/\//i.test(field.value)) {
          field.setCustomValidity('Reference URL must start with http:// or https://.');
        } else if (field.name === 'reference_url') {
          field.setCustomValidity('');
        }
        if (!field.checkValidity()) {
          errorText.textContent = customMessage(field);
          field.reportValidity();
          field.focus();
          field.classList.add('is-invalid');
          return false;
        }
        field.classList.remove('is-invalid');
      }
      errorText.textContent = '';
      return true;
    };
    const fillReview = () => {
      const values = new FormData(briefForm);
      const pairs = [
        ['Name', values.get('name')],
        ['Email', values.get('email')],
        ['Business / Brand', values.get('company')],
        ['Project type', briefForm.querySelector('[name="service_id"]')?.selectedOptions[0]?.text],
        ['Budget range', values.get('budget_range')],
        ['Timeline', values.get('deadline')],
        ['Phone / WhatsApp', values.get('phone')],
        ['Project goal', values.get('goal')],
        ['Target audience', values.get('target_audience')],
        ['Reference link', values.get('reference_url')],
        ['Project details', values.get('scope')],
      ];
      review.innerHTML = pairs.map(([key, value]) => `<div><dt>${key}</dt><dd>${String(value || '-').replace(/[<>&"]/g, c => ({'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;'}[c]))}</dd></div>`).join('');
    };
    const showStep = () => {
      steps.forEach((panel, index) => panel.classList.toggle('is-active', index + 1 === step));
      progress.forEach((item, index) => {
        item.classList.toggle('is-active', index + 1 <= step);
        item.classList.toggle('is-current', index + 1 === step);
        if (index + 1 === step) item.setAttribute('aria-current', 'step');
        else item.removeAttribute('aria-current');
      });
      back.style.visibility = step === 1 ? 'hidden' : 'visible';
      next.style.display = step === 3 ? 'none' : 'inline-flex';
      submit.style.display = step === 3 ? 'inline-flex' : 'none';
      if (step === 3) fillReview();
    };
    next.addEventListener('click', () => {
      if (!validatePanel(steps[step - 1])) return;
      step = Math.min(3, step + 1);
      showStep();
    });
    back.addEventListener('click', () => { step = Math.max(1, step - 1); showStep(); });
    briefForm.addEventListener('submit', (event) => {
      for (let index = 0; index < steps.length; index++) {
        if (!validatePanel(steps[index])) {
          event.preventDefault();
          step = index + 1;
          showStep();
          return;
        }
      }
      submit.disabled = true;
      submit.textContent = 'Sending...';
    });
    showStep();
  }

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
