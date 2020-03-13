<?php
define('API_DIR', __DIR__);
require API_DIR . '/vendor/autoload.php';

$search = new \ArrowComSearch\Search();
$search->getResponse();
