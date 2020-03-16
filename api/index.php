<?php
define('ENV_PATH', __DIR__);
require __DIR__ . '/vendor/autoload.php';

$search = new \PartsSearch\Search;
$search->getResponse();
