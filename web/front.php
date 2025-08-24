<?php

require_once __DIR__.'/../vendor/autoload.php';

use Simplex\StringResponseListener;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;

$routes = include __DIR__.'/../src/app.php';
$container = include __DIR__.'/../src/container.php';

$container->set('routes', $routes);

$container->register('listener.string_response', StringResponseListener::class);
$container->getDefinition('dispatcher')
    ->addMethodCall('addSubscriber', [new Reference('listener.string_response')])
;

$container->setParameter('charset', 'UTF-8');

$request = Request::createFromGlobals();

$response = $container->get('framework')->handle($request);

$response->send();
