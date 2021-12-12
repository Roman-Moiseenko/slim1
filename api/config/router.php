<?php

declare(strict_types=1);

return static function (\Slim\App $app): void {
    $app->get('/', \App\Http\Action\HomeAction::class);
};
