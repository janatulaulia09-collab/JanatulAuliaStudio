<?php
require_once __DIR__.'/../../auth/auth_check.php';
require_admin();
$editId=filter_var($_GET['edit']??'',FILTER_VALIDATE_INT)?:null;
$form=['title'=>'','slug'=>'','excerpt'=>'','content'=>'','cover_image'=>'','tags'=>'','status'=>'draft'];
$error=null;
if($editId){
 $find=$pdo->prepare('SELECT * FROM articles WHERE id=:id');$find->execute(['id'=>$editId]);$existing=$find->fetch();
 if(!$existing){flash('error','Artikel tidak ditemukan.');redirect(app_url('admin.php?page=articles'));}$form=$existing;
}
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){
 verify_post_csrf();$action=(string)($_POST['action']??'save');
 if($action==='delete'){
  $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
  if($id){$pdo->prepare('DELETE FROM articles WHERE id=:id')->execute(['id'=>$id]);flash('success','Artikel berhasil dihapus.');}else flash('error','ID artikel tidak valid.');
  redirect(app_url('admin.php?page=articles'));
 }
 $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT)?:null;$title=trim((string)($_POST['title']??''));$excerpt=trim((string)($_POST['excerpt']??''));$content=trim((string)($_POST['content']??''));$cover=trim((string)($_POST['cover_image']??''));$tags=trim((string)($_POST['tags']??''));$status=in_array($_POST['status']??'',['draft','published'],true)?$_POST['status']:'draft';
 $form=['title'=>$title,'excerpt'=>$excerpt,'content'=>$content,'cover_image'=>$cover,'tags'=>$tags,'status'=>$status];
 $validCover=$cover===''||safe_http_url($cover)||(preg_match('#^[a-zA-Z0-9_./-]+$#',$cover)&&!str_contains($cover,'..'));
 if(mb_strlen($title)<3||mb_strlen($title)>180||mb_strlen($content)<20||mb_strlen($content)>100000||mb_strlen($excerpt)>5000||!$validCover){$error='Periksa judul, ringkasan, isi artikel, dan alamat gambar.';}else{
  $slugBase=slugify($title);$slug=$slugBase;$suffix=2;
  while(true){if($id){$q=$pdo->prepare('SELECT id FROM articles WHERE slug=:slug AND id<>:id');$q->execute(['slug'=>$slug,'id'=>$id]);}else{$q=$pdo->prepare('SELECT id FROM articles WHERE slug=:slug');$q->execute(['slug'=>$slug]);}if(!$q->fetchColumn())break;$slug=$slugBase.'-'.$suffix++;}
  if($id){$save=$pdo->prepare('UPDATE articles SET title=:title,slug=:slug,excerpt=:excerpt,content=:content,cover_image=:cover,status=:status,published_at=IF(:status_pub=\'published\',COALESCE(published_at,NOW()),NULL),tags=:tags WHERE id=:id');$save->execute(['title'=>$title,'slug'=>$slug,'excerpt'=>$excerpt?:null,'content'=>$content,'cover'=>$cover?:null,'status'=>$status,'status_pub'=>$status,'tags'=>$tags?:null,'id'=>$id]);}else{$save=$pdo->prepare('INSERT INTO articles(created_by,title,slug,excerpt,content,cover_image,tags,status,published_at) VALUES(:created_by,:title,:slug,:excerpt,:content,:cover,:tags,:status,IF(:status_pub=\'published\',NOW(),NULL))');$save->execute(['created_by'=>$_SESSION['user']['id'],'title'=>$title,'slug'=>$slug,'excerpt'=>$excerpt?:null,'content'=>$content,'cover'=>$cover?:null,'tags'=>$tags?:null,'status'=>$status,'status_pub'=>$status]);}
  flash('success',$id?'Artikel berhasil diperbarui.':'Artikel berhasil ditambahkan.');redirect(app_url('admin.php?page=articles'));
 }
}
$items=$pdo->query('SELECT id,title,status,published_at,cover_image FROM articles ORDER BY id DESC')->fetchAll();
?>
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7"><div><p class="text-xs text-stone-500">Tulisan, strategi, dan proses kreatif</p><h2 class="serif text-3xl mt-2">Artikel / Insight.</h2></div><a href="insight.php" target="_blank" rel="noopener" class="text-xs font-semibold text-[#98744d]">Lihat tampilan publik ↗</a></div>
<div class="grid xl:grid-cols-[.9fr_1.1fr] gap-5"><section class="bg-white border border-stone-100 rounded-2xl p-6"><h3 class="font-bold text-sm"><?=$editId?'Ubah artikel':'Tambah artikel'?></h3><?php if($error):?><p class="mt-4 bg-rose-50 text-rose-700 rounded-lg p-3 text-xs"><?=e($error)?></p><?php endif;?>
<form method="post" class="mt-5 space-y-4"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=e($editId??'')?>">
<label class="block text-xs font-semibold">Judul artikel<input name="title" required maxlength="180" value="<?=e($form['title'])?>" class="admin-input mt-2"></label>
<label class="block text-xs font-semibold">Ringkasan<textarea name="excerpt" rows="3" maxlength="5000" class="admin-input mt-2"><?=e($form['excerpt'])?></textarea></label>
<label class="block text-xs font-semibold">Isi artikel<textarea name="content" rows="10" maxlength="100000" required class="admin-input mt-2"><?=e($form['content'])?></textarea></label>
<label class="block text-xs font-semibold">Path gambar / URL gambar<input name="cover_image" maxlength="255" value="<?=e($form['cover_image']??'')?>" class="admin-input mt-2" placeholder="assets/images/artikel.jpg"></label>
<label class="block text-xs font-semibold">Tag (pisahkan koma)<input name="tags" maxlength="500" value="<?=e($form['tags']??'')?>" class="admin-input mt-2" placeholder="branding, strategi, desain"></label>
<label class="block text-xs font-semibold">Status<select name="status" class="admin-input mt-2"><option value="draft" <?=$form['status']==='draft'?'selected':''?>>Draft</option><option value="published" <?=$form['status']==='published'?'selected':''?>>Published</option></select></label>
<div class="flex gap-2"><button name="action" value="save" class="bg-[#1c2b27] text-white rounded-full px-6 py-3 text-xs font-bold">Simpan artikel</button><?php if($editId):?><a href="admin.php?page=articles" class="border border-stone-200 rounded-full px-6 py-3 text-xs">Batal</a><?php endif;?></div></form></section>
<section class="bg-white rounded-2xl border border-stone-100 overflow-hidden"><div class="p-5 border-b border-stone-100"><h3 class="font-bold text-sm">Daftar artikel</h3><p class="text-[11px] text-stone-400 mt-1"><?=count($items)?> item tersimpan</p></div><div class="divide-y divide-stone-100"><?php foreach($items as $item):?><div class="p-4 flex gap-4 items-center"><div class="w-16 h-16 bg-stone-100 shrink-0 overflow-hidden rounded-lg"><?php if(!empty($item['cover_image'])):?><img src="<?=e($item['cover_image'])?>" alt="" class="w-full h-full object-cover"><?php endif;?></div><div class="min-w-0 flex-1"><p class="font-semibold text-xs truncate"><?=e($item['title'])?></p><p class="text-[10px] text-stone-400 mt-1"><?=e($item['status'])?><?php if($item['published_at']):?> · <?=date('d M Y',strtotime($item['published_at']))?><?php endif;?></p></div><a href="admin.php?page=articles&edit=<?=(int)$item['id']?>" class="text-[11px] text-[#98744d] font-semibold">Edit</a><form method="post" onsubmit="return confirm('Hapus artikel ini?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$item['id']?>"><button name="action" value="delete" class="text-[11px] text-rose-600">Hapus</button></form></div><?php endforeach; ?><?php if(!$items):?><p class="p-8 text-center text-xs text-stone-400">Belum ada artikel.</p><?php endif;?></div></section></div>
