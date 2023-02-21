<?php

use App\Admin\AdminModule;
use App\Car\CarModule;
use App\Home\HomeModule;
use App\User\UserModule;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\RouterDispatcherMiddleware;
use Core\Framework\Middleware\RouterMiddleware;
use Core\Framework\Middleware\TrailingSlashMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Core\App;
use DI\ContainerBuilder;
use function Http\Response\send;

require dirname(__DIR__).'/vendor/autoload.php';



$modules = [
    HomeModule::class,
    CarModule::class,
    AdminModule::class,
    UserModule::class
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

$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware());


if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}