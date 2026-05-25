<?php
/**
 * Logout handler - destroys session and redirects to login page
 */
session_start();
session_destroy();
header('Location: login.php');
exit;
?>
