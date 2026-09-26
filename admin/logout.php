<?php
session_start();
session_destroy();
header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/admin/login.php');
exit;