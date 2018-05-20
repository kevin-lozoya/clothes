<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

session_start();

$baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$baseDir;
define('BASE_URL', $baseUrl);

$dotenv = new Dotenv\Dotenv(__DIR__ .'/private');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => getenv('DB_HOST'),
	'database'  => getenv('DB_NAME'),
	'username'  => getenv('DB_USER'),
	'password'  => getenv('DB_PASS'),
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

$route = $_GET['route'] ?? '/';

use Phroute\Phroute\RouteCollector;

$router = new RouteCollector();

$router->filter('auth', function() {
	if (!isset($_SESSION['user'])) {
		header('Location: '.BASE_URL.'auth/login');
		return false;
	}
});

$router->filter('require_membership_root', function() {
	if (array_search('root', $_SESSION['user']['memberships']) === false) {
		header('Location: '.BASE_URL);
		return false;
	}
});

$router->controller('/', \App\Controllers\MainController::class);
$router->controller('/auth', \App\Controllers\AuthController::class);

$router->group(['before' => 'auth'], function($router) {
	$router->group(['before' => 'require_membership_root'], function($router) {
		$router->controller('/admin', \App\Controllers\Admin\IndexController::class);
		$router->controller('/admin/users', \App\Controllers\Admin\UsersController::class);
		$router->controller('/admin/products', \App\Controllers\Admin\ProductsController::class);
	});
});


$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $route);

echo $response;
?>