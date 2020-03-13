<?php
define('API_DIR', __DIR__);
require __DIR__ . '/vendor/autoload.php';

$search = new \ArrowComSearch\Search();
$search->getResponse();
