<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// simpan kehadiran
if (isset($_POST['simpan'])) {
  $tanggal = $_POST['tanggal'];
  if (!$tanggal) { header("Location: kehadiran.php"); exit; }

  foreach ($_POST['status'] as $id => $status) {
    $id_angg = intval($id);
    $status_clean = mysqli_real_escape_string($conn, $status);

    // cek apakah sudah ada
    $stmt = mysqli_prepare($conn, "SELECT id FROM kehadiran WHERE id_anggota = ? AND tanggal = ?");
    mysqli_stmt_bind_param($stmt, 'is', $id_angg, $tanggal);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
      // update
      $stmt2 = mysqli_prepare($conn, "UPDATE kehadiran SET status = ? WHERE id_anggota = ? AND tanggal = ?");
      mysqli_stmt_bind_param($stmt2, 'sis', $status_clean, $id_angg, $tanggal);
      mysqli_stmt_execute($stmt2);
      mysqli_stmt_close($stmt2);
    } else {
      // insert
      $stmt3 = mysqli_prepare($conn, "INSERT INTO kehadiran (id_anggota, tanggal, status) VALUES (?, ?, ?)");
      mysqli_stmt_bind_param($stmt3, 'iss', $id_angg, $tanggal, $status_clean);
      mysqli_stmt_execute($stmt3);
      mysqli_stmt_close($stmt3);
    }
    mysqli_stmt_close($stmt);
  }
  header("Location: kehadiran.php");
  exit;
}

$anggota = mysqli_query($conn, "SELECT * FROM anggota ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Catat Kehadiran</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Catat Kehadiran</h2>
  <a href="index.php" class="small">← Kembali</a>
  <form method="post" style="margin-top:12px;">
    <label>Tanggal: <input type="date" name="tanggal" required value="<?=date('Y-m-d')?>"></label>
    <table>
      <tr><th>No</th><th>Nama</th><th>Status</th></tr>
      <?php $no=1; while ($r = mysqli_fetch_assoc($anggota)): ?>
      <tr>
        <td><?=$no++?></td>
        <td><?=htmlspecialchars($r['nama'])?></td>
        <td>
          <select name="status[<?=$r['id']?>]">
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
            <option value="Alpha">Alpha</option>
          </select>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
    <button type="submit" name="simpan">Simpan Kehadiran</button>
  </form>
</div>
</body>
</html>
