<?php
declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$aggregator = new ConfigAggregator([
    new PhpFileProvider(__DIR__ . '/common/*.php')]
);

return $aggregator->getMergedConfig();
/*$files = glob(__DIR__ . '/common/*.php');

$configs = array_map(
    static function ($file) {
        return require $file;
    },
    $files
);
return array_merge_recursive(...$configs);
*/