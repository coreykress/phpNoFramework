<?php declare(strict_types = 1);

namespace src;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Http\HttpRequest;
use Http\HttpResponse;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$environment = 'development';

//error handler
$whoops = new Run();
if ($environment !== 'production') {
    $whoops->pushHandler(new PrettyPageHandler());
} else {
    $whoops->pushHandler(function ($e) {
        echo 'error page';
    });
}
$whoops->register();

$injector = include('Dependencies.php');

/** @var HttpRequest $request */
$request = $injector->make('Http\HttpRequest');
/** @var HttpResponse $response */
$response = $injector->make('Http\HttpResponse');

foreach ($response->getHeaders() as $header) {
    header($header, false);
}

$routeDefinitions = function (RouteCollector $r) {
    $routes = include('Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitions);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page Not Found');
        $response->setStatusCode(400);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $class = $injector->make($className);
        $class->$method($vars);
        break;
}
echo $response->getContent();