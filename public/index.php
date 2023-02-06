<?php

use App\Car\CarModule;
use App\Home\HomeModule;
use GuzzleHttp\Psr7\ServerRequest;
use Core\App;
use DI\ContainerBuilder;
use function Http\Response\send;

require dirname(__DIR__).'/vendor/autoload.php';



$modules = [
    HomeModule::class,
    CarModule::class
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__). DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $module) {
    if(!is_null($module::DEFINITIONS)) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

$container = $builder->build();

$app = new App($container, $modules);


if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}