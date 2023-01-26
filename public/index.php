<?php

use App\Car\CarModule;
use App\Home\HomeModule;
use Core\Framework\Renderer\TwigRenderer;
use GuzzleHttp\Psr7\ServerRequest;
use Core\App;
use function Http\Response\send;

require dirname(__DIR__).'/vendor/autoload.php';

$renderer = new TwigRenderer(
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'view'
);

$renderer->addGlobal('siteName', 'RentCar');

$app = new App([
    HomeModule::class,
    CarModule::class
],
['renderer' => $renderer]);
$response = $app->run(ServerRequest::fromGlobals());
send($response);