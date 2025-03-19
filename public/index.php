<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../App/helpers.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Dotenv\Dotenv;
use App\Routing\Route;
use App\Models\Users as UsersModel;
use App\Support\Collection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$request = Request::createFromGlobals();
$context = (new RequestContext())->fromRequest($request);
$matcher = new UrlMatcher(Route::getRoutes(), $context);

try {
    $parameters = $matcher->match($request->getPathInfo());

    if (!isset($parameters['controller']) || !is_array($parameters['controller']) || count($parameters['controller']) !== 2) {
        throw new Exception("No valid controller found for this route.");
    }

    $controller = $parameters['controller'];
    [$class, $method] = $controller;

    if (!class_exists($class)) {
        throw new Exception("Controller class '$class' not found. Check namespace and file.");
    }
    if (!method_exists($class, $method)) {
        throw new Exception("Method '$method' not found in controller class '$class'.");
    }

    $routeName = $parameters['_route'];
    $route = Route::getRoutes()->get($routeName);

    Log::info('Route matched', [
        'route_name' => $routeName,
        'path' => $request->getPathInfo(),
        'middleware' => $route->middleware ?? [],
        'parameters' => $parameters
    ]);

    if (isset($parameters['closure'])) {
        $response = Route::handleClosure($request, $parameters['closure']);
    } else {
        $middleware = $route->middleware ?? [];
        $controllerHandler = function (Request $request) use ($class, $method, $parameters) {
            $controllerInstance = new $class();
            return call_user_func([$controllerInstance, $method], $request, $parameters['id'] ?? null);
        };

        $stack = $controllerHandler;
        foreach (array_reverse($middleware) as $middlewareName) {
            $middlewareClass = Route::$middleware[$middlewareName] ?? $middlewareName;
            $middlewareInstance = new $middlewareClass();
            $stack = function (Request $request) use ($middlewareInstance, $stack) {
                return $middlewareInstance->handle($request, $stack);
            };
        }

        $response = $stack($request);
    }

    if ($response === null) {
        throw new Exception("Controller method '$method' in '$class' did not return a response.");
    }
} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $exception) {
    Log::error('Resource not found', ['exception' => $exception->getMessage(), 'path' => $request->getPathInfo()]);
    $response = new Response(view('errors/404'), 404);
} catch (Exception $exception) {
    Log::error('Unexpected error', ['exception' => $exception->getMessage(), 'path' => $request->getPathInfo()]);
    if ($exception->getCode() === 403) {
        $response = new Response(view('errors/403'), 403);
    } else {
        $response = new Response(view('errors/500'), 500);
    }
}

if (is_string($response)) {
    $response = new Response($response);
}

$response->send();
