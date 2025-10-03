<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);
  $kelas = trim($_POST['kelas']);
  $jabatan = trim($_POST['jabatan']);
  $tahun = intval($_POST['tahun']);

  if ($nama === '' ) $err = 'Nama wajib diisi.';
  else {
    $stmt = mysqli_prepare($conn, "INSERT INTO anggota (nama, kelas, jabatan, tahun_bergabung) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssi', $nama, $kelas, $jabatan, $tahun);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: anggota.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Tambah Anggota</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="wrap">
  <h3>Tambah Anggota</h3>
  <?php if ($err) echo "<div class='error'>".htmlspecialchars($err)."</div>"; ?>
  <form method="post" class="form">
    <label>Nama</label><input name="nama" required>
    <label>Kelas</label><input name="kelas" required>
    <label>Jabatan</label><input name="jabatan" required>
    <label>Tahun Bergabung</label><input type="number" name="tahun" min="2000" max="2099" value="<?=date('Y')?>" required>
    <button type="submit">Simpan</button>
  </form>
  <p><a href="anggota.php">Kembali</a></p>
</div>
</body>
</html>
