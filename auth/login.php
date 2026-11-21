<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!empty($_SESSION['user']['id'])) redirect(app_url('admin.php'));
$error = flash('error');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Sesi keamanan tidak valid. Muat ulang halaman.';
    } elseif (($_SESSION['login_locked_until'] ?? 0) > time()) {
        $error = 'Terlalu banyak percobaan. Coba lagi beberapa menit.';
    } else {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = (string)($_POST['password'] ?? '');
        if (!$email || $password === '') {
            $error = 'Masukkan email dan kata sandi yang valid.';
        } else {
            $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM admin_users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = :id')->execute(['id' => $admin['id']]);
                $_SESSION['user'] = [
                    'id' => (int)$admin['id'],
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'role' => $admin['role'],
                ];
                $_SESSION['login_attempts'] = 0;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                redirect(app_url('admin.php'));
            }

            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_locked_until'] = time() + 300;
                $_SESSION['login_attempts'] = 0;
            }
            $error = 'Email atau kata sandi tidak sesuai.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk Admin | JanatulAulia Studio</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css?v=bright-20261119-02">
  <style>body{font-family:'DM Sans',sans-serif}.serif{font-family:'Playfair Display',serif}</style>
</head>
<body class="min-h-screen bg-[#f4f1ec] flex items-center justify-center p-5">
  <div class="w-full max-w-5xl grid md:grid-cols-2 overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-stone-900/10">
    <div class="hidden md:flex flex-col justify-between bg-[#17211f] p-12 text-white relative overflow-hidden">
      <div class="absolute -right-24 -bottom-24 w-80 h-80 rounded-full border border-white/10"></div><div class="absolute -right-10 -bottom-10 w-52 h-52 rounded-full border border-white/10"></div>
      <a href="<?= e(app_url()) ?>" class="text-sm tracking-[.25em] uppercase text-[#d8b98b]">JanatulAulia Studio</a>
      <div class="relative"><p class="text-xs uppercase tracking-[.3em] text-[#d8b98b] mb-5">Creative workspace</p><h1 class="serif text-5xl leading-tight">Mengelola ide,<br>menciptakan<br><em class="text-[#d8b98b]">makna.</em></h1><p class="mt-7 max-w-sm text-white/60 leading-7">Ruang kerja digital untuk mengelola layanan kreatif dan permintaan proyek JanatulAulia Studio.</p></div>
      <p class="text-xs text-white/40">© <?= date('Y') ?> JanatulAulia Studio</p>
    </div>
    <div class="p-8 sm:p-12 md:p-14 flex flex-col justify-center">
      <a href="<?= e(app_url()) ?>" class="text-xs tracking-[.22em] uppercase text-stone-500 md:hidden mb-10">JanatulAulia Studio</a>
      <p class="text-xs font-bold uppercase tracking-[.24em] text-[#a17b4d]">Admin portal</p><h2 class="serif text-4xl text-[#1b2825] mt-3">Selamat datang.</h2><p class="mt-3 text-stone-500">Masuk untuk mengelola website studio.</p>
      <?php if ($error): ?><div class="mt-7 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="mt-8 space-y-5">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label class="block"><span class="text-sm font-semibold text-stone-700">Email</span><input required type="email" name="email" autocomplete="username" class="mt-2 w-full rounded-xl border border-stone-200 px-4 py-3 outline-none focus:border-[#a17b4d] focus:ring-2 focus:ring-[#a17b4d]/10" placeholder="admin@janatulaulia.studio"></label>
        <label class="block"><span class="text-sm font-semibold text-stone-700">Kata sandi</span><input required type="password" name="password" autocomplete="current-password" class="mt-2 w-full rounded-xl border border-stone-200 px-4 py-3 outline-none focus:border-[#a17b4d] focus:ring-2 focus:ring-[#a17b4d]/10" placeholder="Masukkan kata sandi"></label>
        <button class="w-full rounded-xl bg-[#1c2b27] py-3.5 text-sm font-bold text-white hover:bg-[#294039] transition">Masuk ke Dashboard</button>
      </form>
      <p class="mt-7 text-xs text-stone-400">Akses hanya untuk admin terdaftar. <a href="<?= e(app_url()) ?>" class="text-[#a17b4d] hover:underline">Kembali ke website</a></p>
    </div>
  </div>
</body></html>
