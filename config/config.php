<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('charset', $_ENV['CHARSET']);
define('BASE_URL', $_ENV['BASE_URL']);
define('ROOT_PATH', dirname(__DIR__));

error_reporting(E_ALL);
ini_set('display_errors', 1);
