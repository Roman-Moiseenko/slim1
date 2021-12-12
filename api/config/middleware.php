<?php

declare(strict_types=1);

return static function (\Slim\App $app, \DI\Container $container): void {
    $app->addErrorMiddleware($container->get('config')['debug'], false, true);

};
