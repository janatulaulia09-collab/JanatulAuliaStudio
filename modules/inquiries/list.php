<?php
require_once __DIR__ . '/../../auth/auth_check.php';
require_admin();
$allowed = ['New', 'Contacted', 'Qualified', 'Proposal', 'Won', 'Lost'];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post_csrf();
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $status = (string)($_POST['status'] ?? '');
    if ($id && in_array($status, $allowed, true)) {
        $update = $pdo->prepare('UPDATE leads SET status=:status WHERE id=:id');
        $update->execute(['status'=>$status,'id'=>$id]);
        flash('success', 'Status Project Brief berhasil diperbarui.');
    } else {
        flash('error', 'Data perubahan status tidak valid.');
    }
    redirect(app_url('admin.php?page=inquiries'));
}
$statusFilter = (string)($_GET['status'] ?? '');
$sql = 'SELECT l.*, s.name AS service_name FROM leads l LEFT JOIN services s ON s.id=l.service_id';
$params = [];
if (in_array($statusFilter, $allowed, true)) {
    $sql .= ' WHERE l.status = :status';
    $params['status'] = $statusFilter;
}
$sql .= ' ORDER BY l.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>
<div class="mb-7"><p class="text-xs text-stone-500">Permintaan proyek dari calon klien</p><h2 class="serif text-3xl mt-2">Project Brief.</h2><p class="text-sm text-stone-500 mt-2">Tinjau kebutuhan, data kontak, dan perkembangan setiap permintaan proyek.</p></div>
<div class="flex flex-wrap gap-2 mb-5"><a href="admin.php?page=inquiries" class="rounded-full px-4 py-2 text-xs border <?=$statusFilter===''?'bg-[#1c2b27] text-white':'border-stone-200'?>">Semua</a><?php foreach($allowed as $st): ?><a href="?page=inquiries&status=<?=urlencode($st)?>" class="rounded-full px-4 py-2 text-xs border <?=$statusFilter===$st?'bg-[#1c2b27] text-white':'border-stone-200'?>"><?=e($st)?></a><?php endforeach; ?></div>
<div class="space-y-4"><?php foreach($items as $item): ?><article class="bg-white border border-stone-100 rounded-2xl p-5 sm:p-6"><div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4"><div><div class="flex flex-wrap items-center gap-3"><h3 class="font-bold"><?=e($item['name'])?></h3><span class="text-[10px] rounded-full bg-amber-50 text-amber-700 px-3 py-1"><?=e($item['status'])?></span></div><p class="text-xs text-stone-500 mt-2"><?=e($item['email'])?> · <?=e($item['phone'])?></p><p class="text-[11px] text-stone-400 mt-1"><?=e($item['company']?:'Nama brand tidak diisi')?> · <?=date('d M Y H:i',strtotime($item['created_at']))?></p></div><form method="post" class="flex gap-2 items-center"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><select name="status" class="admin-input !w-auto"><?php foreach($allowed as $st): ?><option value="<?=e($st)?>" <?=$item['status']===$st?'selected':''?>><?=e($st)?></option><?php endforeach; ?></select><button class="rounded-lg bg-[#1c2b27] text-white px-4 py-3 text-xs">Perbarui</button></form></div>
<div class="grid sm:grid-cols-3 gap-4 mt-5 border-t border-stone-100 pt-5"><div><p class="text-[10px] uppercase tracking-wider text-stone-400">Layanan</p><p class="text-xs mt-2"><?=e($item['service_name']?:'Belum ditentukan')?></p></div><div><p class="text-[10px] uppercase tracking-wider text-stone-400">Anggaran / Deadline</p><p class="text-xs mt-2"><?=e($item['budget_range']?:'Belum ditentukan')?> · <?=e($item['deadline']?:'Belum ditentukan')?></p></div><div><p class="text-[10px] uppercase tracking-wider text-stone-400">Tujuan proyek</p><p class="text-xs mt-2"><?=e($item['goal']?:'-')?></p></div></div>
<?php if (!empty($item['target_audience'])): ?><div class="mt-4"><p class="text-[10px] uppercase tracking-wider text-stone-400 mb-2">Target audiens</p><p class="text-xs text-stone-600"><?=e($item['target_audience'])?></p></div><?php endif; ?>
<?php if (!empty($item['reference_url'])): ?><div class="mt-3"><a class="text-xs text-[#98744d]" href="<?=e($item['reference_url'])?>" target="_blank" rel="noopener">Buka referensi proyek ↗</a></div><?php endif; ?>
<div class="mt-5 bg-[#f7f6f2] rounded-xl p-4"><p class="text-[10px] uppercase tracking-wider text-stone-400 mb-2">Detail brief</p><p class="text-xs leading-6 text-stone-600 whitespace-pre-line"><?=e($item['scope']?:$item['notes']?:'-')?></p></div><div class="mt-4"><a class="text-xs font-semibold text-[#98744d]" href="https://wa.me/<?=preg_replace('/\D/','',$item['phone'])?>" target="_blank" rel="noopener">Hubungi melalui WhatsApp ↗</a></div></article><?php endforeach; ?><?php if(!$items): ?><div class="bg-white rounded-2xl border border-stone-100 p-12 text-center text-sm text-stone-400">Belum ada Project Brief pada filter ini.</div><?php endif; ?></div>
