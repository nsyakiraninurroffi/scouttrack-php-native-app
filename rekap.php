<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$rekap = mysqli_query($conn, "
  SELECT 
    a.id, a.nama,
    SUM(CASE WHEN k.status='Hadir' THEN 1 ELSE 0 END) AS hadir,
    SUM(CASE WHEN k.status='Izin' THEN 1 ELSE 0 END) AS izin,
    SUM(CASE WHEN k.status='Sakit' THEN 1 ELSE 0 END) AS sakit,
    SUM(CASE WHEN k.status='Alpha' THEN 1 ELSE 0 END) AS alpha
  FROM anggota a
  LEFT JOIN kehadiran k ON k.id_anggota = a.id
  GROUP BY a.id, a.nama
  ORDER BY a.nama
");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Rekap Kehadiran</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Rekap Kehadiran</h2>
  <div style="display:flex;gap:8px;margin-bottom:10px;">
    <a href="index.php" class="small">← Kembali</a>
    <a href="printable_rekap.php" target="_blank" class="small">Cetak (browser)</a>
    <a href="cetak_rekap_pdf.php" target="_blank" class="small">Cetak PDF</a>
  </div>

  <table>
    <tr><th>Nama</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th></tr>
    <?php while ($r = mysqli_fetch_assoc($rekap)): ?>
    <tr>
      <td><?=htmlspecialchars($r['nama'])?></td>
      <td><?=$r['hadir']?></td>
      <td><?=$r['izin']?></td>
      <td><?=$r['sakit']?></td>
      <td><?=$r['alpha']?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
