<?php
require_once '../includes/session.php';

// Destroy session and redirect
session_destroy();
header('Location: ../index.php');
exit();
?>
