<?php
require_once __DIR__ . '/../../auth/auth_check.php';
require_admin();

$editId = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT) ?: null;
$form = [
    'name' => '',
    'description' => '',
    'deliverables' => '',
    'duration_label' => '',
    'status' => 'draft',
];
$error = null;

if ($editId) {
    $find = $pdo->prepare('SELECT id, name, description, deliverables, duration_label, status FROM services WHERE id = :id');
    $find->execute(['id' => $editId]);
    $existing = $find->fetch();
    if (!$existing) {
        flash('error', 'Layanan tidak ditemukan.');
        redirect(app_url('admin.php?page=services'));
    }
    $form = $existing;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post_csrf();
    $action = $_POST['action'] ?? 'save';
    if ($action === 'delete') {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) {
            $del = $pdo->prepare('DELETE FROM services WHERE id = :id');
            $del->execute(['id' => $id]);
            flash('success', 'Layanan berhasil dihapus. Riwayat lead tetap disimpan.');
        } else {
            flash('error', 'ID layanan tidak valid.');
        }
        redirect(app_url('admin.php?page=services'));
    }

    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT) ?: null;
    $name = trim((string)($_POST['name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $deliverables = trim((string)($_POST['deliverables'] ?? ''));
    $duration = trim((string)($_POST['duration_label'] ?? ''));
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft';
    $form = compact('name', 'description', 'deliverables', 'duration');
    $form['duration_label'] = $duration;
    $form['status'] = $status;

    if (mb_strlen($name) < 3 || mb_strlen($name) > 120 || mb_strlen($description) < 10 || mb_strlen($description) > 5000 || mb_strlen($deliverables) < 5 || mb_strlen($deliverables) > 5000) {
        $error = 'Nama, deskripsi, atau hasil layanan belum memenuhi ketentuan panjang karakter.';
    } else {
        $slugBase = slugify($name);
        $slug = $slugBase;
        $suffix = 2;
        while (true) {
            if ($id) {
                $check = $pdo->prepare('SELECT id FROM services WHERE slug = :slug AND id <> :current_id LIMIT 1');
                $check->execute(['slug' => $slug, 'current_id' => $id]);
            } else {
                $check = $pdo->prepare('SELECT id FROM services WHERE slug = :slug LIMIT 1');
                $check->execute(['slug' => $slug]);
            }
            if (!$check->fetchColumn()) break;
            $slug = $slugBase . '-' . $suffix++;
        }

        if ($id) {
            $save = $pdo->prepare('UPDATE services SET name=:name, slug=:slug, description=:description, deliverables=:deliverables, duration_label=:duration, status=:status WHERE id=:id');
            $save->execute(['name'=>$name,'slug'=>$slug,'description'=>$description,'deliverables'=>$deliverables,'duration'=>$duration !== '' ? $duration : null,'status'=>$status,'id'=>$id]);
        } else {
            $save = $pdo->prepare('INSERT INTO services (created_by, name, slug, description, deliverables, duration_label, status) VALUES (:admin, :name, :slug, :description, :deliverables, :duration, :status)');
            $save->execute(['admin'=>$_SESSION['user']['id'],'name'=>$name,'slug'=>$slug,'description'=>$description,'deliverables'=>$deliverables,'duration'=>$duration !== '' ? $duration : null,'status'=>$status]);
        }
        flash('success', $id ? 'Layanan berhasil diperbarui.' : 'Layanan berhasil ditambahkan.');
        redirect(app_url('admin.php?page=services'));
    }
}

$items = $pdo->query('SELECT id, name, slug, description, duration_label, status FROM services ORDER BY id DESC')->fetchAll();
?>
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7"><div><p class="text-xs text-stone-500">Daftar katalog layanan kreatif</p><h2 class="serif text-3xl mt-2">Layanan studio.</h2><p class="text-sm text-stone-500 mt-2">Hanya layanan berstatus Published yang ditampilkan di website publik.</p></div><a href="admin.php?page=services" class="rounded-full bg-[#1c2b27] text-white px-5 py-3 text-xs font-bold">＋ Layanan baru</a></div>
<div class="grid xl:grid-cols-[.9fr_1.1fr] gap-5">
  <section class="bg-white rounded-2xl border border-stone-100 p-6 sm:p-7">
    <h3 class="font-bold text-sm"><?=$editId ? 'Ubah layanan' : 'Tambah layanan'?></h3>
    <p class="text-[11px] text-stone-400 mt-1">Isi informasi resmi sebelum mengubah status menjadi Published.</p>
    <?php if ($error): ?><p class="mt-4 bg-rose-50 text-rose-700 rounded-lg p-3 text-xs"><?=e($error)?></p><?php endif; ?>
    <form method="post" class="mt-5 space-y-4">
      <input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=e($editId ?? '')?>">
      <label class="block text-xs font-semibold">Nama layanan<input name="name" required maxlength="120" value="<?=e($form['name'])?>" class="admin-input mt-2" placeholder="Contoh: Branding & Identity"></label>
      <label class="block text-xs font-semibold">Deskripsi layanan<textarea name="description" required rows="4" maxlength="5000" class="admin-input mt-2" placeholder="Jelaskan tujuan dan ruang lingkup layanan..."><?=e($form['description'])?></textarea></label>
      <label class="block text-xs font-semibold">Hasil / deliverables<textarea name="deliverables" required rows="3" maxlength="5000" class="admin-input mt-2" placeholder="Tuliskan hasil layanan yang akan diterima klien..."><?=e($form['deliverables'])?></textarea></label>
      <label class="block text-xs font-semibold">Label durasi (opsional)<input name="duration_label" maxlength="100" value="<?=e($form['duration_label'] ?? '')?>" class="admin-input mt-2" placeholder="Misalnya: Estimasi dibahas saat konsultasi"></label>
      <label class="block text-xs font-semibold">Status<select name="status" class="admin-input mt-2"><option value="draft" <?=($form['status']==='draft'?'selected':'')?>>Draft (belum tampil)</option><option value="published" <?=($form['status']==='published'?'selected':'')?>>Published (tampil publik)</option></select></label>
      <div class="flex gap-2"><button class="bg-[#1c2b27] text-white rounded-full px-6 py-3 text-xs font-bold">Simpan layanan</button><?php if($editId): ?><a href="admin.php?page=services" class="border border-stone-200 rounded-full px-6 py-3 text-xs">Batal</a><?php endif; ?></div>
    </form>
  </section>
  <section class="bg-white rounded-2xl border border-stone-100 overflow-hidden"><div class="p-5 border-b border-stone-100"><h3 class="font-bold text-sm">Daftar layanan</h3><p class="text-[11px] text-stone-400 mt-1"><?=count($items)?> layanan tersimpan</p></div>
    <div class="divide-y divide-stone-100"><?php foreach($items as $item): ?><div class="p-5 flex flex-col sm:flex-row sm:items-center gap-3"><div class="min-w-0 flex-1"><p class="font-semibold text-sm"><?=e($item['name'])?></p><p class="text-[11px] text-stone-400 mt-1"><?=e(mb_strimwidth($item['description'],0,110,'…'))?></p><span class="inline-block mt-2 rounded-full px-3 py-1 text-[10px] <?=$item['status']==='published'?'bg-emerald-50 text-emerald-700':'bg-stone-100 text-stone-500'?>"><?=e(ucfirst($item['status']))?></span></div><div class="flex gap-3 items-center"><a href="admin.php?page=services&id=<?=(int)$item['id']?>" class="text-xs font-semibold text-[#98744d]">Edit</a><form method="post" onsubmit="return confirm('Hapus layanan ini? Lead yang pernah memilih layanan ini tetap disimpan.')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><button name="action" value="delete" class="text-xs text-rose-600">Hapus</button></form></div></div><?php endforeach; ?><?php if(!$items): ?><p class="p-10 text-center text-xs text-stone-400">Belum ada layanan. Tambahkan layanan menggunakan formulir ini.</p><?php endif; ?></div>
  </section>
</div>
