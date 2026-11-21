<?php
require_once __DIR__ . '/../config/bootstrap.php';
http_response_code(410);
exit('Pendaftaran publik dinonaktifkan. Tambahkan admin baru melalui proses administrasi lokal yang aman.');
