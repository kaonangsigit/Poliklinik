<?php
session_start();

// Hapus semua sesi
session_unset();
session_destroy();

// Redirect ke halaman awal (root directory)
header("Location: ../index.php");
exit();
?> 