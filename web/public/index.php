<?php

declare(strict_types=1);

include '../app/vendor/autoload.php';

//use App\Acme\Core\Database;
use App\Acme\Core\Router;

// Гарантируем наличие таблицы feedback до обработки запроса
//Database::getInstance();
$router = new Router();
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
