<?php
require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$db = new Database();
$auth = new Auth($db);
$auth->logout();
header('Location: login.php');
exit;