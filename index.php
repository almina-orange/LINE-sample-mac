<?php
// all read library installed by composer
require_once __DIR__ . '/vendor/autoload.php';

// get and preview values sent in POST method
$inputString = file_get_contents('php://input');
error_log($inputString);
?>
