<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admins_login.php");
    exit;
}
header("Location: admins_dashboard.php");
exit;
