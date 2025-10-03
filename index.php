<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// total anggota
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM anggota"))['c'];

// statistik 7 hari terakhir
$stats = [];
$res = mysqli_query($conn, "
  SELECT status, COUNT(*) as cnt 
  FROM kehadiran 
  WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY status
");
while ($r = mysqli_fetch_assoc($res)) {
  $stats[$r['status']] = $r['cnt'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard - ScoutTrack</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #001D39, #0A4174);
      color: #333;
      margin: 0;
      box-sizing: border-box;    
    }
    header {
      text-align: center;
      margin-bottom: 30px;
    }
    header h1 {
      font-size: 28px;
      color: #0A4174;
    }
    .muted {
      font-size: 14px;
      color: #4E8EA2;
    }
    .meta {
      margin-top: 10px;
      font-size: 14px;
    }
    .meta a {
      color: #860021ff;
      text-decoration: none;
      margin-left: 10px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      padding: 20px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card h3 {
      font-size: 16px;
      color: #790e0eff;
      margin-bottom: 10px;
    }
    .card .big {
      font-size: 24px;
      font-weight: 700;
      color: #000000ff;
    }
    .menu {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 30px;
      justify-content: center;
    }
    .btn {
      background: #0c0434ff;
      color: #fff;
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background: #909fe1ff;
    }
    .btn-green {
      background: #0c0434ff;
      color: #ffffffff;
    }
    .btn-green:hover {
      background: #909fe1ff;
    }
    section h2 {
      font-size: 20px;
      margin-bottom: 20px;
      text-align: center;
      color: #f0f0f0;
    }
    .chart-container {
      max-width: 350px;
      margin: auto;
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(10px);
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .legend {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 15px;
      font-size: 14px;
      color: #071e48ff;
    }
    .legend span {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .circle {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
    }
    @media (max-width: 600px) {
      .cards {
        grid-template-columns: 1fr;
      }

    }
  </style>

</head>
<body>
  <div class="container">
    <header>
      <div>
        <h1>ScoutTrack</h1>
        <p class="muted">Pendataan Anggota & Kehadiran Pramuka</p>
      </div>
      <div class="meta">
        <span>Halo, <strong><?=htmlspecialchars($_SESSION['username'] ?? 'Admin')?></strong></span>
        <a href="logout.php" class="btn-ghost">Logout</a>
      </div>
    </header>

    <div class="cards">
      <div class="card"><h3>Total Anggota</h3><p class="big"><?= $total ?></p></div>
      <div class="card"><h3>Hadir (7d)</h3><p class="big"><?= $stats['Hadir'] ?? 0 ?></p></div>
      <div class="card"><h3>Izin (7d)</h3><p class="big"><?= $stats['Izin'] ?? 0 ?></p></div>
      <div class="card"><h3>Sakit (7d)</h3><p class="big"><?= $stats['Sakit'] ?? 0 ?></p></div>
      <div class="card"><h3>Alpha (7d)</h3><p class="big"><?= $stats['Alpha'] ?? 0 ?></p></div>
    </div>

    <nav class="menu">
      <a href="anggota.php" class="btn">Kelola Anggota</a>
      <a href="kehadiran.php" class="btn">Catat Kehadiran</a>
      <a href="rekap.php" class="btn">Rekap Kehadiran</a>
      <a href="cetak_rekap_pdf.php" target="_blank" class="btn btn-green">Cetak Rekap (PDF)</a>
    </nav>

        <section>
      <h2>Grafik Kehadiran (7 Hari)</h2>
      <div class="chart-container">
        <canvas id="myPieChart"></canvas>
        <div class="legend">
          <span><span class="circle" style="background:#00ff26ff;"></span> Hadir</span>
          <span><span class="circle" style="background:#0A4174;"></span> Izin</span>
          <span><span class="circle" style="background:#95a5a6;"></span> Sakit</span>
          <span><span class="circle" style="background:#e74c3c;"></span> Alpha</span>
        </div>
      </div>
    </section>

  </div>
  <script>
    const ctx = document.getElementById('myPieChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
          data: [
            <?= $stats['Hadir'] ?? 0 ?>,
            <?= $stats['Izin'] ?? 0 ?>,
            <?= $stats['Sakit'] ?? 0 ?>,
            <?= $stats['Alpha'] ?? 0 ?>
          ],
          backgroundColor: [
            '#00d431ff',
            '#230963ff',
            '#95a5a6',
            '#e74c3c'
          ],
          hoverOffset: 20
        }]
      },
      options: {
        responsive: true,
        cutout: '60%',
        animation: {
          animateScale: true,
          animateRotate: true
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  </script>
</body>
</html>
