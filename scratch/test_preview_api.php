<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'preview';
$_GET['import_id'] = 'IMP-TEST-734';

chdir(__DIR__ . '/../api/attendance');
require_once 'imports.php';
?>
