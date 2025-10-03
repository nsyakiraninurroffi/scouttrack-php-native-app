<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$where = "1=1";
$params = [];
$types = '';

if (!empty($_GET['q'])) {
  $where .= " AND nama LIKE ?";
  $q = '%'.$_GET['q'].'%';
  $params[] = $q; $types .= 's';
}
if (!empty($_GET['kelas'])) {
  $where .= " AND kelas = ?";
  $params[] = $_GET['kelas']; $types .= 's';
}
if (!empty($_GET['jabatan'])) {
  $where .= " AND jabatan = ?";
  $params[] = $_GET['jabatan']; $types .= 's';
}

$sql = "SELECT * FROM anggota WHERE $where ORDER BY nama";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Anggota - ScoutTrack</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Data Anggota</h2>
  <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
    <input name="q" placeholder="Cari nama" value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
    <input name="kelas" placeholder="Kelas" value="<?=htmlspecialchars($_GET['kelas'] ?? '')?>">
    <input name="jabatan" placeholder="Jabatan" value="<?=htmlspecialchars($_GET['jabatan'] ?? '')?>">
    <button type="submit">Filter</button>
    <a href="anggota.php" class="small">Reset</a>
  </form>

  <div style="display:flex;gap:8px;margin-bottom:10px;">
    <a href="tambah_anggota.php" class="btn">+ Tambah Anggota</a>
    <a href="index.php" class="small">← Kembali</a>
  </div>

  <table>
    <tr><th>No</th><th>Nama</th><th>Kelas</th><th>Jabatan</th><th>Tahun</th><th>Aksi</th></tr>
    <?php $no=1; while ($r = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($r['nama']) ?></td>
      <td><?= htmlspecialchars($r['kelas']) ?></td>
      <td><?= htmlspecialchars($r['jabatan']) ?></td>
      <td><?= htmlspecialchars($r['tahun_bergabung']) ?></td>
      <td>
        <a href="edit_anggota.php?id=<?= $r['id'] ?>">Edit</a> |
        <a href="hapus_anggota.php?id=<?= $r['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
