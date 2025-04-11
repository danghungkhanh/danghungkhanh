<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
redirect(SITE_URL . 'login.php');
?> 