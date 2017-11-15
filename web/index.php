<?php
/**
 * 159.339 Internet Programming 2017.2
 * System: PHP 7.1
 * Code guidelines: PSR-1, PSR-2
 *
 * FRONT CONTROLLER - Responsible for URL routing and User Authentication
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 **/
date_default_timezone_set('Pacific/Auckland');

require __DIR__ . '/vendor/autoload.php';

use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

define('APP_ROOT', __DIR__);

$collection = new RouteCollection();

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login', array(
            '_controller' => 'internet\a3\controller\CustomerController::indexAction',
            'methods' => 'GET',
            'name' => 'Login'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login', array(
            '_controller' => 'internet\a3\controller\CustomerController::indexAction',
            'methods' => 'POST',
            'name' => 'Login'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/register/', array(
        '_controller' => 'internet\a3\controller\CustomerController::registerAction',
        'methods' => 'GET',
        'name' => 'Register'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/create/', array(
        '_controller' => 'internet\a3\controller\CustomerController::createAction',
        'methods' => 'POST',
        'name' => 'Welcome'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login/', array(
            '_controller' => 'internet\a3\controller\CustomerController::loginAction',
            'methods' => 'GET',
            'name' => 'Login'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login/welcome/', array(
            '_controller' => 'internet\a3\controller\CustomerController::welcomeAction',
            'methods' => 'POST',
            'name' => 'Welcome'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login/welcome/', array(
            '_controller' => 'internet\a3\controller\CustomerController::welcomeAction',
            'methods' => 'GET',
            'name' => 'Welcome'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/error', array(
            '_controller' => 'internet\a3\controller\CustomerController::errorAction',
            'methods' => 'POST',
            'name' => 'Error'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login/search', array(
            '_controller' => 'internet\a3\controller\CustomerController::searchAction',
            'methods' => 'GET',
            'name' => 'Search'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/ToolshedInc/login/browse', array(
            '_controller' => 'internet\a3\controller\CustomerController::browseAction',
            'methods' => 'GET',
            'name' => 'Browse'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/searching', array(
            '_controller' => 'internet\a3\controller\AjaxController::getProductsBySearch',
            'methods' => 'POST',
            'name' => 'searching'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/browsing', array(
            '_controller' => 'internet\a3\controller\AjaxController::getProductsByBrowse',
            'methods' => 'POST',
            'name' => 'browsing'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/usernamecheck', array(
            '_controller' => 'internet\a3\controller\AjaxController::checkUserNameExist',
            'methods' => 'POST',
            'name' => 'usernamecheck'
        )
    )
);

$router = new Router($collection);
$router->setBasePath('/');

$route = $router->matchCurrentRequest();

// If route was dispatched successfully - return
if ($route) {
    // true indicates to webserver that the route was successfully served
    return true;
}

// Otherwise check if the request is for a static resource
$info = parse_url($_SERVER['REQUEST_URI']);
// check if its allowed static resource type and that the file exists
if (preg_match('/\.(?:png|jpg|jpeg|css|js)$/', "$info[path]")
    && file_exists("./$info[path]")
) {
    // false indicates to web server that the route is for a static file - fetch it and return to client
    return false;
} else {
    header("HTTP/1.0 404 Not Found");
    // Custom error page
    // require 'static/html/404.html';
    return true;
}