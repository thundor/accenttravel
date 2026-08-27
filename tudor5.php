<?php
ini_set('display_errors', 1);
ini_set('error_reporting', -1);
echo '<pre>';
echo date('H:i:s') . PHP_EOL;
require_once(__DIR__ . '/tudor6.php');
FLocker::acquire('test');
sleep(3);
echo date('H:i:s') . PHP_EOL;