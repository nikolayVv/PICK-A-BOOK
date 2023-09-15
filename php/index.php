<?php

define('__ROOT__', dirname(dirname(__FILE__)));

require_once('./vendor/autoload.php');

use app\core\Application;
use app\controllers\AuthController;
use app\controllers\HomeController;
use app\controllers\ProfileController;
use app\controllers\BooksRESTController;
use app\models\User;

$config = [
    'userClass' => User::class,
    'db' => [
        'host' => 'localhost',
        'dbname' => 'bookstore',
        'username' => 'root',
        'password' => 'ep'
    ]
];

$app = new Application($_SERVER["SCRIPT_NAME"] . "/", $config);

//authorization
$app->router->get('', [AuthController::class, 'login']);
$app->router->post('', [AuthController::class, 'login']);
$app->router->get('register', [AuthController::class, 'register']);
$app->router->post('register', [AuthController::class, 'register']);
$app->router->get('logout', [AuthController::class, 'logout']);
$app->router->get('profile', [AuthController::class, 'profile']);
$app->router->get('cert', [AuthController::class, 'cert']);
$app->router->post('cert', [AuthController::class, 'cert']);

//store
$app->router->get('store', [HomeController::class, 'bookList']);
$app->router->get('store/addBook', [HomeController::class, 'addBook']);
$app->router->post('store/addBook', [HomeController::class, 'addBook']);
$app->router->get('store/bookDetails', [HomeController::class, 'bookDetails']);
$app->router->get('store/cart', [HomeController::class, 'cart']);
$app->router->post('store/cart', [HomeController::class, 'cart']);
$app->router->get('store/makeOrder', [HomeController::class, 'makeOrder']);
$app->router->post('store/makeOrder', [HomeController::class, 'makeOrder']);
$app->router->post('store/addInCart', [HomeController::class, 'addInCart']);
$app->router->get('store/allCustomers', [HomeController::class, 'allCustomers']);
$app->router->get('store/allSellers', [HomeController::class, 'allSellers']);
$app->router->get('store/editUser', [HomeController::class, 'editUser']);
$app->router->post('store/editUser', [HomeController::class, 'editUser']);
$app->router->get('store/changeStatus', [HomeController::class, 'changeStatus']);
$app->router->get('store/addUser', [HomeController::class, 'addUser']);
$app->router->post('store/addUser', [HomeController::class, 'addUser']);
$app->router->get('store/editBook', [HomeController::class, 'editBook']);
$app->router->post('store/editBook', [HomeController::class, 'editBook']);

//profile
$app->router->get('profile/editProfile', [ProfileController::class, 'editProfile']);
$app->router->post('profile/editProfile', [ProfileController::class, 'editProfile']);
$app->router->get('profile/pendingOrders', [ProfileController::class, 'pendingOrders']);
$app->router->get('profile/approvedOrders', [ProfileController::class, 'approvedOrders']);
$app->router->get('profile/allOrders', [ProfileController::class, 'allOrders']);
$app->router->get('profile/myOrders', [ProfileController::class, 'myOrders']);
$app->router->get('profile/viewOrder', [ProfileController::class, 'viewOrder']);
$app->router->get('profile/editOrder', [ProfileController::class, 'editOrder']);
$app->router->post('profile/editOrder', [ProfileController::class, 'editOrder']);

//REST API
$app->router->get('api/books', [BooksRESTController::class, 'bookList']);

$app->run();