<?php
session_start();

// Alleen normale gebruiker uitloggen.
// Admin blijft ingelogd totdat je op admin_logout.php klikt.
unset($_SESSION['gebruiker_id']);
unset($_SESSION['gebruiker_naam']);
unset($_SESSION['gebruiker_email']);

header('Location: index.php');
exit;
?>
