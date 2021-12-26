<?php
declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;


http_response_code(500);
require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ .'/../config/container.php';

$cli = new \Symfony\Component\Console\Application('Console');
$commands = $container->get('config')['console']['commands'];
$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');
ConsoleRunner::addCommands($cli);
foreach ($commands as $command) {
    $cli->add($container->get($command));
}
$cli->run();