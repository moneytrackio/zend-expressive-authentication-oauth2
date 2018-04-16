<?php
use Psr\Container\ContainerInterface;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Console;
use ZF\Console\Application;

chdir(__DIR__ . '/../../../../');
require_once 'vendor/autoload.php';

define('VERSION', '1.0.0');

/** @var ContainerInterface $container */
$container = require 'config/container.php';

$routes = [
    [
        'name' => 'generate-keys',
        'route' => '[--path=] [--dest-path=] [--accessTokenExpiry=] [--refreshTokenExpiry=] [--authCodeExpiry=]',
        'description' => 'Generate public, private and encryption keys for thephpleague/oauth2-server.',
        'short_description' => 'Generate keys.',
        'options_descriptions' => [
            '--keys-path'   => 'Path to store the generated keys',
            '--config-path' => 'Path to store the config file',
            '--accessTokenExpiry' => 'The access token life period, in DateInterval format.',
            '--refreshTokenExpiry' => 'The refresh token life period, in DateInterval format.',
            '--authCodeExpiry' => 'The auth code life period, in DateInterval format.',
        ],
        'defaults' => [
            'keys-path'   => 'data',
            'config-path' => 'config',
            'accessTokenExpiry' => 'P1D', // 1 day in DateInterval format
            'refreshTokenExpiry' => 'P1M', // 1 month in DateInterval format
            'authCodeExpiry' => 'PT10M', // 10 minutes in DateInterval format
        ],
        'handler' => function($route, $console) use ($container) {
            $handler = $container->get(Zend\Expressive\Authentication\OAuth2\Console\GenerateKeys::class);
            return $handler($route, $console);
        }
    ],
    [
        'name' => 'with-pdo-adapter',
        'route' => '--dsn= --username= --password= [--config-path=]',
        'description' => 'Define PDO as the adapter to use with OAuth2.',
        'short_description' => 'PDO OAuth2 adapter',
        'options_descriptions' => [
            '--dsn' => 'Data Source Name to connect DB',
            '--username' => 'Username of the DB instance',
            '--password' => 'Password of the DB instance user',
        ],
        'defaults' => [
            'config-path' => 'config',
        ],
        'handler' => function($route, $console) use ($container) {
            $handler = $container->get(Zend\Expressive\Authentication\OAuth2\Console\PdoAdapter::class);
            return $handler($route, $console);
        }
    ],
    [
        'name' => 'with-doctrine-adapter',
        'route' => '--userClass= --userField= [--service-alias=] [--config-path=]',
        'description' => 'Define Doctrine as the adapter to use with OAuth2.',
        'short_description' => 'Doctrine OAuth2 adapter',
        'options_descriptions' => [
            '--userClass' => 'FQCN of the oauth user entity',
            '--userField' => 'Field of the user entity to use as unique identifier',
            '--service_alias' => 'The container service alias of the Doctrine entity manager',
            '--config-path' => 'Path to store the config file',
        ],
        'defaults' => [
            'service_alias' => 'doctrine.entity_manager.orm_default',
            'configPath' => 'config',
        ],
        'handler' => function($route, $console) use ($container) {
            if (!$container->has($service = $route->getMatchedParam('service_alias'))) {
                $console->writeLine(sprintf("Could not retrieve '%s' service in container.\n", $service), Color::RED);
                return 1;
            }
            $handler = $container->get(Zend\Expressive\Authentication\OAuth2\Console\DoctrineAdapter::class);
            return $handler($route, $console);
        }
    ],
];

$app = new Application(
    'Zend Expressive Authentication OAuth2',
    VERSION,
    $routes,
    Console::getInstance()
);
$exit = $app->run();
exit($exit);