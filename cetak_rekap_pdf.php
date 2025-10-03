<?php
// Jika belum install dompdf, gunakan printable_rekap.php
// Untuk pakai dompdf, jalankan: composer require dompdf/dompdf
require 'vendor/autoload.php';
use Dompdf\Dompdf;

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

$html = '<h2 style="text-align:center;">Rekap Kehadiran Anggota</h2>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">';
$html .= '<tr><th>Nama</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th></tr>';
while ($r = mysqli_fetch_assoc($rekap)) {
  $html .= '<tr><td>'.htmlspecialchars($r['nama']).'</td><td>'.$r['hadir'].'</td><td>'.$r['izin'].'</td><td>'.$r['sakit'].'</td><td>'.$r['alpha'].'</td></tr>';
}
$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("rekap_kehadiran.pdf", ["Attachment" => false]);
exit;
