<?php

use Slim\Factory\AppFactory;

return static function (\Psr\Container\ContainerInterface $container) {
    $app = AppFactory::createFromContainer($container);
    (require __DIR__ . '/../config/middleware.php')($app, $container);
    (require __DIR__ . '/../config/router.php')($app);
    return $app;
};