<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$id = intval($_GET['id'] ?? 0);
$row = null;
if ($id) {
  $stmt = mysqli_prepare($conn, "SELECT id,nama,kelas,jabatan,tahun_bergabung FROM anggota WHERE id = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);
}
if (!$row) { header("Location: anggota.php"); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);
  $kelas = trim($_POST['kelas']);
  $jabatan = trim($_POST['jabatan']);
  $tahun = intval($_POST['tahun']);
  if ($nama === '') $err = 'Nama wajib diisi.';
  else {
    $stmt = mysqli_prepare($conn, "UPDATE anggota SET nama=?, kelas=?, jabatan=?, tahun_bergabung=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssii', $nama, $kelas, $jabatan, $tahun, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: anggota.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Edit Anggota</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="wrap">
  <h3>Edit Anggota</h3>
  <?php if ($err) echo "<div class='error'>".htmlspecialchars($err)."</div>"; ?>
  <form method="post" class="form">
    <label>Nama</label><input name="nama" value="<?=htmlspecialchars($row['nama'])?>" required>
    <label>Kelas</label><input name="kelas" value="<?=htmlspecialchars($row['kelas'])?>" required>
    <label>Jabatan</label><input name="jabatan" value="<?=htmlspecialchars($row['jabatan'])?>" required>
    <label>Tahun Bergabung</label><input type="number" name="tahun" min="2000" max="2099" value="<?=htmlspecialchars($row['tahun_bergabung'])?>" required>
    <button type="submit">Update</button>
  </form>
  <p><a href="anggota.php">Kembali</a></p>
</div>
</body>
</html>
