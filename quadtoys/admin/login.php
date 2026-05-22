<?php
// Compatibilidade: redireciona para o sistema de login existente
session_start();
if (isset($_SESSION['admin'])) {
    header("Location: admins_dashboard.php");
} else {
    header("Location: admins_login.php");
}
exit;
