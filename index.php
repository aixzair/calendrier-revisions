<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use Api\RevisionAPI;
use Controllers\RevisionController;

// Démarre la session
session_start();

// Charge le fichier .env
Dotenv::createImmutable(__DIR__)->load();

// Ajoute les routes
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
	$r->addGroup('/revision', function (RouteCollector $r) {
		$r->addRoute('GET', '/create', [new RevisionController(), 'create']);
		$r->addRoute('POST', '/createPost', [new RevisionController(), 'createPost']);
		$r->addRoute('GET', '/programme', [new RevisionController(), 'programme']);
	});

	// Api
	$r->addGroup('/apib', function (RouteCollector $r) {
		$r->addGroup('/revision', function (RouteCollector $r) {
			$r->addRoute('GET', '/calendrier', [new RevisionAPI(), 'calendrier']);
		});
	});
});


// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);


switch ($routeInfo[0]) {
	case Dispatcher::NOT_FOUND:
		// TODO : ... 404 Not Found
	break;
	case Dispatcher::METHOD_NOT_ALLOWED:
		$allowedMethods = $routeInfo[1];
		// TODO : ... 405 Method Not Allowed
	break;
	case Dispatcher::FOUND:
		$handler = $routeInfo[1];
		$vars = $routeInfo[2];
		call_user_func_array($handler, $vars);
	break;
}
