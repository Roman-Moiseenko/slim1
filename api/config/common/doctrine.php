<?php
declare(strict_types=1);

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    \Doctrine\ORM\EntityManagerInterface::class => function (\DI\Container $container): \Doctrine\ORM\EntityManagerInterface {
        $settings = $container->get('config')['doctrine'];
        $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir']
                ? DoctrineProvider::wrap(new FilesystemAdapter('', 0, $settings['cache_dir']))
                : DoctrineProvider::wrap(new ArrayAdapter()),
            false
        );
        $config->setNamingStrategy(new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy());
        return \Doctrine\ORM\EntityManager::create(
            $settings, $config
        );
    },
    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => 'localhost:5432',
                'user' => 'postgres',
                'password' => 'admin',
                'dbname' => 'slim',
                'charset' => 'utf-8'
            ],
            'metadata_dirs' => [
                '/src/Auth/Entity',
            ]
        ]
    ]
];