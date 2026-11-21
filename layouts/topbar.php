<div class="flex-1 min-w-0"><header class="admin-topbar h-20 bg-white border-b border-stone-200 px-5 sm:px-8 flex items-center justify-between"><div><p class="text-[10px] uppercase tracking-[.2em] text-stone-400">JanatulAulia Studio / Admin</p><h1 class="serif text-xl mt-1"><?php $titles=['dashboard'=>'Ringkasan','services'=>'Layanan Studio','produk'=>'Layanan Studio','tambah_produk'=>'Layanan Studio','portfolio'=>'Portofolio','inquiries'=>'Project Brief']; echo e($titles[$page]??'Dashboard'); ?></h1></div><div class="flex items-center gap-3"><span class="hidden sm:block text-xs text-stone-500">Halo, <?=e($_SESSION['user']['name']??'Admin')?></span><span class="w-10 h-10 rounded-full bg-[#eae5dc] flex items-center justify-center text-[#795c3d] serif text-lg"><?=e(mb_substr($_SESSION['user']['name']??'A',0,1))?></span><div class="lg:hidden"><form method="post" action="auth/logout.php"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><button class="text-xs text-stone-500">Keluar</button></form></div></div></header>
<nav class="admin-mobile-nav lg:hidden" aria-label="Navigasi admin">
  <a href="admin.php?page=dashboard" class="<?=($page==='dashboard'?'active':'')?>">Ringkasan</a>
  <a href="admin.php?page=services" class="<?=in_array($page,['services','produk','tambah_produk'],true)?'active':''?>">Layanan</a>
  <a href="admin.php?page=portfolio" class="<?=($page==='portfolio'?'active':'')?>">Portofolio</a>
  <a href="admin.php?page=inquiries" class="<?=($page==='inquiries'?'active':'')?>">Brief</a>
</nav>
<main class="admin-main p-5 sm:p-8 max-w-[1500px] mx-auto">
<?php if($notice=flash('success')): ?><div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 px-4 py-3 text-sm"><?=e($notice)?></div><?php endif; ?>
<?php if($notice=flash('error')): ?><div class="mb-6 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 px-4 py-3 text-sm"><?=e($notice)?></div><?php endif; ?>
