<?php
include 'config.php';
$rekap = mysqli_query($conn, "
  SELECT 
    a.nama,
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
<head><meta charset="utf-8"><title>Cetak Rekap</title>
<style>
body{font-family:Arial;padding:20px}
table{width:100%;border-collapse:collapse}
table th, table td{border:1px solid #333;padding:8px;text-align:left}
</style>
</head>
<body onload="window.print();">
  <h2>Rekap Kehadiran Anggota</h2>
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
</body>
</html>
