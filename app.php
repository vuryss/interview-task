<?php

require __DIR__ . '/vendor/autoload.php';

use App\Command\CalculateCommissionsCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$containerBuilder = new ContainerBuilder();
$fileLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/config'));
$fileLoader->load('container.yaml');
$containerBuilder->setParameter('root_dir', __DIR__);
$containerBuilder->compile();

$application = new Application();
$application->add($containerBuilder->get(CalculateCommissionsCommand::class));

try {
    $application->run();
} catch (Exception $e) {
    // TODO: Handle exception
    throw $e;
}
