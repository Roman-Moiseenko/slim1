<?php
declare(strict_types=1);

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/dependencies.php');
return $container = $builder->build();
