<?php
require 'db.php';
$logger = new Log();
$logger->write('HTTP Method received: ' . $_SERVER['REQUEST_METHOD']);
echo 'HTTP Method received: ' . $_SERVER['REQUEST_METHOD'];
?>