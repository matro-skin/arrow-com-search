<?php
define('API_DIR', __DIR__);
require API_DIR . '/vendor/autoload.php';

$search = new \PartsSearch\Search;
$search->getResponse();
