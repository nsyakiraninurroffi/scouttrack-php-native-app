<?php
// generate_hash.php — jalankan sekali di browser untuk buat hash password
$plain = "ZieGp-01163x164!"; // ganti kalau mau
$hash = password_hash($plain, PASSWORD_DEFAULT);
echo "Plain: $plain<br>";
echo "Hash: <input style='width:100%' value='$hash'>";
