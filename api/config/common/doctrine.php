<?php
declare(strict_types=1);

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManagerInterface::class => function (\DI\Container $container): EntityManagerInterface {
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
        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }
        return \Doctrine\ORM\EntityManager::create(
            $settings['connection'], $config
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
                __DIR__ . '/../../src/Auth/Entity',
            ],
            'types' => [
                App\Auth\Entity\User\IdType::NAME => App\Auth\Entity\User\IdType::class,
                App\Auth\Entity\User\EmailType::NAME => App\Auth\Entity\User\EmailType::class,
                App\Auth\Entity\User\RoleType::NAME => App\Auth\Entity\User\RoleType::class,
                App\Auth\Entity\User\StatusType::NAME => App\Auth\Entity\User\StatusType::class,
            ],
        ]
    ]
];