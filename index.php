<?php

require_once __DIR__ . '/vendor/autoload.php';

// Allows us to require templates easily from root directory
define('ROOT', __DIR__ . '/');

use App\Http\Controllers\QuizController;
use App\Services\QuizService;
use App\DB\Database;
use App\Services\UserService;

/**
 * Environment variable setup
 */
$env = parse_ini_file('.env');

if (!$env) {
    throw new ErrorException('Missing .env configuration file');
}

foreach ($env as $varName => $varValue) {
    $_ENV[$varName] = $varValue;
}

$dbSettings = [
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_DATABASE'],
    'user' => $_ENV['DB_USER'],
    'pass' => $_ENV['DB_PASSWORD']
];

$db = new Database($dbSettings);

/**
 * Router
 */
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

switch ($request_uri[0]) {
    case '/':
        $controller = new QuizController(new QuizService($db), new UserService($db));
        $controller->index();

        break;
    case '/quiz/start':
        $controller = new QuizController(new QuizService($db), new UserService($db));
        $controller->startQuiz();

        break;
    case '/quiz/':
        $controller = new QuizController(new QuizService($db), new UserService($db));
        $controller->showQuiz();

        break;
    case '/quiz/answer':
        $controller = new QuizController(new QuizService($db), new UserService($db));
        $controller->submitAnswer();

        break;
    case '/quiz/done':
        $controller = new QuizController(new QuizService($db), new UserService($db));
        $controller->showCompletedQuiz();

        break;
    default:
        header('HTTP/1.0 404 Not Found');
        break;
}
