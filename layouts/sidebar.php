<aside class="admin-sidebar w-[250px] shrink-0 bg-[#17211f] text-white hidden lg:flex flex-col min-h-screen">
  <div class="px-6 h-24 flex items-center border-b border-white/10"><a href="<?= e(app_url()) ?>" class="flex items-center gap-3"><span class="w-10 h-10 rounded-full border border-[#d5b58b] flex items-center justify-center serif text-xl text-[#e4c49b]">J</span><span><strong class="block tracking-[.15em] uppercase text-xs">JanatulAulia</strong><small class="tracking-[.3em] uppercase text-[9px] text-white/45">Studio admin</small></span></a></div>
  <div class="px-4 py-7 flex-1"><p class="px-3 text-[9px] tracking-[.25em] uppercase text-white/30 mb-3">Workspace</p><nav class="space-y-1">
    <a href="admin.php?page=dashboard" class="side-link <?=($page==='dashboard'?'active':'')?>"><span>◫</span> Ringkasan</a>
    <a href="admin.php?page=services" class="side-link <?=in_array($page,['services','produk','tambah_produk'],true)?'active':''?>"><span>▤</span> Layanan Studio</a>
    <a href="admin.php?page=portfolio" class="side-link <?=($page==='portfolio'?'active':'')?>"><span>▧</span> Portofolio</a>
    <a href="admin.php?page=articles" class="side-link <?=($page==='articles'?'active':'')?>"><span>✎</span> Artikel / Insight</a>
    <a href="admin.php?page=inquiries" class="side-link <?=($page==='inquiries'?'active':'')?>"><span>✉</span> Project Brief</a>
  </nav><p class="px-3 text-[9px] tracking-[.25em] uppercase text-white/30 mt-9 mb-3">Website</p><a href="<?= e(app_url()) ?>" target="_blank" rel="noopener" class="side-link"><span>↗</span> Lihat Website</a></div>
  <div class="p-4 border-t border-white/10"><div class="px-3 py-3"><p class="text-sm font-semibold"><?=e($_SESSION['user']['name']??'Admin')?></p><p class="text-[10px] text-white/40 mt-1"><?=e(ucfirst($_SESSION['user']['role']??'admin'))?></p></div><form method="post" action="auth/logout.php" class="px-3"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><button class="text-xs text-white/50 hover:text-white">Keluar dari dashboard ↗</button></form></div>
</aside>
