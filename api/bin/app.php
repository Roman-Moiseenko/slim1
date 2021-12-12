<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;


http_response_code(500);
require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ .'/../config/container.php';

$cli = new \Symfony\Component\Console\Application('Console');
$commands = $container->get('config')['console']['commands'];
foreach ($commands as $command) {
    $cli->add($container->get($command));
}
$cli->run();