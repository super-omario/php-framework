<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$requestStack = new RequestStack();
$routes = include __DIR__.'/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher, $requestStack));

$listener = new HttpKernel\EventListener\ErrorListener(
    'Calendar\Controller\ErrorController::exception'
);
$dispatcher->addSubscriber($listener);

$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

$dispatcher->addSubscriber(new Simplex\StringResponseListener());

$framework = new Simplex\Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);

$response = $framework->handle($request);
$response->send();
