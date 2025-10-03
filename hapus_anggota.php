<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id) {
  $stmt = mysqli_prepare($conn, "DELETE FROM anggota WHERE id = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}
header("Location: anggota.php");
exit;
