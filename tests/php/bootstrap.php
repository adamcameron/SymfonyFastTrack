<?php
use Symfony\Component\Dotenv\Dotenv;

$rootDir = dirname(__DIR__, 2);

require $rootDir . '/vendor/autoload.php';

if (file_exists($rootDir . '/config/bootstrap.php')) {
    require $rootDir . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv($rootDir . '/.env.test');
}
