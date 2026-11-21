<?php
require_once __DIR__ . '/../../auth/auth_check.php';
require_admin();

$editId = filter_var($_GET['edit'] ?? '', FILTER_VALIDATE_INT) ?: null;
$services = $pdo->query('SELECT name, slug FROM services ORDER BY name')->fetchAll();
$form = ['title'=>'','category_name'=>'','service_slug'=>'','year'=>(int)date('Y'),'summary'=>'','description'=>'','cover_image'=>'assets/images/portfolio-placeholder.svg','status'=>'draft'];
$error = null;

if ($editId) {
    $find = $pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM projects p INNER JOIN project_categories c ON c.id=p.category_id WHERE p.id=:id');
    $find->execute(['id'=>$editId]);
    $existing = $find->fetch();
    if (!$existing) {
        flash('error', 'Portofolio tidak ditemukan.');
        redirect(app_url('admin.php?page=portfolio'));
    }
    $form = $existing;
    $form['service_slug'] = in_array($existing['category_slug'], array_column($services, 'slug'), true)
        ? $existing['category_slug']
        : '';
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post_csrf();
    $action = (string)($_POST['action'] ?? 'save');
    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) {
            $delete = $pdo->prepare('DELETE FROM projects WHERE id=:id');
            $delete->execute(['id'=>$id]);
            flash('success', 'Portofolio berhasil dihapus.');
        } else flash('error', 'ID portofolio tidak valid.');
        redirect(app_url('admin.php?page=portfolio'));
    }

    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT) ?: null;
    $title = trim((string)($_POST['title'] ?? ''));
    $categoryName = trim((string)($_POST['category_name'] ?? ''));
    $serviceSlug = trim((string)($_POST['service_slug'] ?? ''));
    $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
    $summary = trim((string)($_POST['summary'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $coverImage = trim((string)($_POST['cover_image'] ?? ''));
    $status = in_array($_POST['status'] ?? '', ['draft','published'], true) ? $_POST['status'] : 'draft';
    $form = ['title'=>$title,'category_name'=>$categoryName,'service_slug'=>$serviceSlug,'year'=>$year ?: date('Y'),'summary'=>$summary,'description'=>$description,'cover_image'=>$coverImage,'status'=>$status];

    $isValidImage = $coverImage !== '' && (
        safe_http_url($coverImage) ||
        (preg_match('#^[a-zA-Z0-9_./-]+$#', $coverImage) && !str_contains($coverImage, '..'))
    );
    if (mb_strlen($title) < 3 || mb_strlen($title) > 160 || ($serviceSlug === '' && (mb_strlen($categoryName) < 2 || mb_strlen($categoryName) > 80)) || !$year || $year < 2000 || $year > (int)date('Y') + 1 || mb_strlen($summary) < 5 || mb_strlen($summary) > 5000 || mb_strlen($description) < 10 || mb_strlen($description) > 30000 || !$isValidImage) {
        $error = 'Periksa judul, kategori, tahun, ringkasan, deskripsi, dan alamat gambar. Gunakan URL http/https atau path lokal seperti assets/images/karya.jpg.';
    } else {
        $service = null;
        if ($serviceSlug !== '') {
            $findService = $pdo->prepare('SELECT name, slug FROM services WHERE slug=:slug LIMIT 1');
            $findService->execute(['slug'=>$serviceSlug]);
            $service = $findService->fetch();
            if (!$service) {
                $error = 'Layanan terkait tidak ditemukan. Pilih layanan yang tersedia.';
            } else {
                $categoryName = $service['name'];
            }
        }

        if (!$error) {
            if ($service) {
                $categoryLookup = $pdo->prepare('SELECT id FROM project_categories WHERE slug=:slug LIMIT 1');
                $categoryLookup->execute(['slug'=>$service['slug']]);
                $categoryId = $categoryLookup->fetchColumn();

                if (!$categoryId) {
                    $categoryLookup = $pdo->prepare('SELECT id, slug FROM project_categories WHERE name=:name LIMIT 1');
                    $categoryLookup->execute(['name'=>$service['name']]);
                    $existingCategory = $categoryLookup->fetch();
                    if ($existingCategory) {
                        $slugCheck = $pdo->prepare('SELECT id FROM project_categories WHERE slug=:slug LIMIT 1');
                        $slugCheck->execute(['slug'=>$service['slug']]);
                        if ($slugCheck->fetchColumn()) {
                            $error = 'Kategori layanan ini sudah digunakan kategori lain. Periksa kategori portofolio.';
                        } else {
                            $updateCategory = $pdo->prepare('UPDATE project_categories SET slug=:slug WHERE id=:id');
                            $updateCategory->execute(['slug'=>$service['slug'],'id'=>$existingCategory['id']]);
                            $categoryId = $existingCategory['id'];
                        }
                    } else {
                        $catInsert = $pdo->prepare('INSERT INTO project_categories (name, slug) VALUES (:name,:slug)');
                        $catInsert->execute(['name'=>$service['name'],'slug'=>$service['slug']]);
                        $categoryId = (int)$pdo->lastInsertId();
                    }
                }
            } else {
                $categorySlugBase = slugify($categoryName);
                $categoryLookup = $pdo->prepare('SELECT id FROM project_categories WHERE name=:name LIMIT 1');
                $categoryLookup->execute(['name'=>$categoryName]);
                $categoryId = $categoryLookup->fetchColumn();
                if (!$categoryId) {
                    $catSlug = $categorySlugBase;
                    $suffix = 2;
                    while (true) {
                        $catCheck = $pdo->prepare('SELECT id FROM project_categories WHERE slug=:slug LIMIT 1');
                        $catCheck->execute(['slug'=>$catSlug]);
                        if (!$catCheck->fetchColumn()) break;
                        $catSlug = $categorySlugBase . '-' . $suffix++;
                    }
                    $catInsert = $pdo->prepare('INSERT INTO project_categories (name, slug) VALUES (:name,:slug)');
                    $catInsert->execute(['name'=>$categoryName,'slug'=>$catSlug]);
                    $categoryId = (int)$pdo->lastInsertId();
                }
            }

            if (!$error) {
                $slugBase = slugify($title);
                $slug = $slugBase;
                $suffix = 2;
                while (true) {
                    if ($id) {
                        $slugCheck = $pdo->prepare('SELECT id FROM projects WHERE slug=:slug AND id<>:current_id LIMIT 1');
                        $slugCheck->execute(['slug'=>$slug,'current_id'=>$id]);
                    } else {
                        $slugCheck = $pdo->prepare('SELECT id FROM projects WHERE slug=:slug LIMIT 1');
                        $slugCheck->execute(['slug'=>$slug]);
                    }
                    if (!$slugCheck->fetchColumn()) break;
                    $slug = $slugBase . '-' . $suffix++;
                }

                if ($id) {
                    $save = $pdo->prepare('UPDATE projects SET category_id=:category_id, title=:title, slug=:slug, year=:year, summary=:summary, description=:description, cover_image=:cover_image, status=:status, published_at=IF(:status_pub=\'published\', COALESCE(published_at,NOW()), NULL) WHERE id=:id');
                    $save->execute(['category_id'=>$categoryId,'title'=>$title,'slug'=>$slug,'year'=>$year,'summary'=>$summary,'description'=>$description,'cover_image'=>$coverImage,'status'=>$status,'status_pub'=>$status,'id'=>$id]);
                } else {
                    $save = $pdo->prepare('INSERT INTO projects (category_id, created_by, title, slug, year, summary, description, cover_image, status, published_at) VALUES (:category_id,:created_by,:title,:slug,:year,:summary,:description,:cover_image,:status,IF(:status_pub=\'published\',NOW(),NULL))');
                    $save->execute(['category_id'=>$categoryId,'created_by'=>$_SESSION['user']['id'],'title'=>$title,'slug'=>$slug,'year'=>$year,'summary'=>$summary,'description'=>$description,'cover_image'=>$coverImage,'status'=>$status,'status_pub'=>$status]);
                }
                flash('success', $id ? 'Portofolio berhasil diperbarui.' : 'Portofolio berhasil ditambahkan.');
                redirect(app_url('admin.php?page=portfolio'));
            }
        }
    }
}

$categories = $pdo->query('SELECT name FROM project_categories ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
$items = $pdo->query('SELECT p.id,p.title,p.year,p.cover_image,p.status,c.name AS category_name FROM projects p INNER JOIN project_categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
?>
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7"><div><p class="text-xs text-stone-500">Karya dan studi kasus kreatif</p><h2 class="serif text-3xl mt-2">Portofolio.</h2></div><a href="portfolio.php" target="_blank" rel="noopener" class="text-xs font-semibold text-[#98744d]">Lihat tampilan publik ↗</a></div>
<div class="grid xl:grid-cols-[.9fr_1.1fr] gap-5"><section class="bg-white border border-stone-100 rounded-2xl p-6"><h3 class="font-bold text-sm"><?=$editId?'Ubah portofolio':'Tambah portofolio'?></h3><p class="text-[11px] text-stone-400 mt-1">Gunakan gambar milik studio atau gambar yang sudah mendapat izin.</p><?php if($error): ?><p class="mt-4 bg-rose-50 text-rose-700 rounded-lg p-3 text-xs"><?=e($error)?></p><?php endif; ?>
<form method="post" class="mt-5 space-y-4"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=e($editId??'')?>">
<label class="block text-xs font-semibold">Judul karya<input name="title" required maxlength="160" value="<?=e($form['title'])?>" class="admin-input mt-2"></label>
<div class="grid grid-cols-2 gap-3"><label class="block text-xs font-semibold">Layanan terkait (opsional)<select name="service_slug" class="admin-input mt-2"><option value="">Kategori umum</option><?php foreach($services as $service): ?><option value="<?=e($service['slug'])?>" <?=$form['service_slug']===$service['slug']?'selected':''?>><?=e($service['name'])?></option><?php endforeach; ?></select></label><label class="block text-xs font-semibold">Tahun<input type="number" name="year" min="2000" max="<?=date('Y')+1?>" required value="<?=e($form['year'])?>" class="admin-input mt-2"></label></div>
<label class="block text-xs font-semibold">Kategori portofolio<input name="category_name" list="project-categories" maxlength="80" value="<?=e($form['category_name'])?>" class="admin-input mt-2" placeholder="Wajib diisi jika tidak memilih layanan"><datalist id="project-categories"><?php foreach($categories as $cat): ?><option value="<?=e($cat)?>"><?php endforeach; ?></datalist></label>
<label class="block text-xs font-semibold">Ringkasan singkat<textarea name="summary" rows="2" maxlength="5000" required class="admin-input mt-2"><?=e($form['summary'])?></textarea></label>
<label class="block text-xs font-semibold">Deskripsi lengkap<textarea name="description" rows="4" maxlength="30000" required class="admin-input mt-2"><?=e($form['description'])?></textarea></label>
<label class="block text-xs font-semibold">Path gambar / URL gambar<input name="cover_image" required maxlength="255" value="<?=e($form['cover_image'])?>" class="admin-input mt-2" placeholder="assets/images/nama-karya.jpg"></label>
<label class="block text-xs font-semibold">Status<select name="status" class="admin-input mt-2"><option value="draft" <?=$form['status']==='draft'?'selected':''?>>Draft</option><option value="published" <?=$form['status']==='published'?'selected':''?>>Published</option></select></label>
<div class="flex gap-2"><button name="action" value="save" class="bg-[#1c2b27] text-white rounded-full px-6 py-3 text-xs font-bold">Simpan portofolio</button><?php if($editId): ?><a href="admin.php?page=portfolio" class="border border-stone-200 rounded-full px-6 py-3 text-xs">Batal</a><?php endif; ?></div></form></section>
<section class="bg-white rounded-2xl border border-stone-100 overflow-hidden"><div class="p-5 border-b border-stone-100"><h3 class="font-bold text-sm">Daftar karya</h3><p class="text-[11px] text-stone-400 mt-1"><?=count($items)?> item tersimpan</p></div><div class="divide-y divide-stone-100"><?php foreach($items as $item): ?><div class="p-4 flex gap-4 items-center"><div class="w-16 h-16 bg-stone-100 shrink-0 overflow-hidden rounded-lg"><img src="<?=e($item['cover_image'])?>" alt="" class="w-full h-full object-cover"></div><div class="min-w-0 flex-1"><p class="font-semibold text-xs truncate"><?=e($item['title'])?></p><p class="text-[10px] text-stone-400 mt-1"><?=e($item['category_name'])?> · <?=e($item['year'])?> · <?=e($item['status'])?></p></div><a href="admin.php?page=portfolio&edit=<?=(int)$item['id']?>" class="text-[11px] text-[#98744d] font-semibold">Edit</a><form method="post" onsubmit="return confirm('Hapus portofolio ini?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><button name="action" value="delete" class="text-[11px] text-rose-600">Hapus</button></form></div><?php endforeach; ?><?php if(!$items): ?><p class="p-8 text-center text-xs text-stone-400">Belum ada karya.</p><?php endif; ?></div></section></div>
