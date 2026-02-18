<?php

declare(strict_types=1);

use App\Application\UseCase\GetProjectDocUseCase;
use App\Application\UseCase\ListProjectsUseCase;
use App\Application\UseCase\LoginUseCase;
use App\Application\UseCase\LogoutUseCase;
use App\Infrastructure\Config\Env;
use App\Infrastructure\DocFetcher\DocxDownloader;
use App\Infrastructure\DocFetcher\DocxToHtmlConverter;
use App\Infrastructure\DocFetcher\HtmlSanitizer;
use App\Infrastructure\Persistence\PdoConnection;
use App\Infrastructure\Persistence\PdoProjectDocRepository;
use App\Infrastructure\Persistence\PdoProjectRepository;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Infrastructure\Security\AuthMiddleware;
use App\Infrastructure\Security\SessionManager;
use App\UI\Controller\ApiController;
use App\UI\Controller\AuthController;
use App\UI\Controller\DashboardController;

require dirname(__DIR__) . '/vendor/autoload.php';

$env = new Env(dirname(__DIR__) . '/.env');
$sessionManager = new SessionManager($env->get('SESSION_NAME', 'HEXAGONAL_PASS'));

try {
    $pdo = (new PdoConnection($env))->create();
} catch (Throwable $exception) {
    http_response_code(500);
    echo '<h1>Error de conexion</h1><p>No fue posible conectar con la base de datos.</p>';
    exit;
}

$userRepository = new PdoUserRepository($pdo);
$projectRepository = new PdoProjectRepository($pdo);
$projectDocRepository = new PdoProjectDocRepository($pdo);

$loginUseCase = new LoginUseCase($userRepository);
$logoutUseCase = new LogoutUseCase($sessionManager);
$listProjectsUseCase = new ListProjectsUseCase($projectRepository);
$getProjectDocUseCase = new GetProjectDocUseCase(
    $projectRepository,
    $projectDocRepository,
    new DocxDownloader(),
    new DocxToHtmlConverter(),
    new HtmlSanitizer(),
    $env->getInt('CACHE_TTL_HOURS', 24)
);

$authController = new AuthController($loginUseCase, $logoutUseCase, $sessionManager);
$dashboardController = new DashboardController($sessionManager);
$apiController = new ApiController($listProjectsUseCase, $getProjectDocUseCase);
$authMiddleware = new AuthMiddleware($sessionManager);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? $path : '/';

if ($method === 'GET' && $path === '/login') {
    $authController->showLogin();
    exit;
}

if ($method === 'POST' && $path === '/login') {
    $authController->login($_POST);
    exit;
}

if ($method === 'GET' && $path === '/logout') {
    $authController->logout();
    exit;
}

$authMiddleware->requireAuth();

if ($method === 'GET' && ($path === '/' || $path === '/dashboard')) {
    $dashboardController->index();
    exit;
}

if ($method === 'GET' && $path === '/api/projects') {
    $apiController->projects();
    exit;
}

if ($method === 'GET' && preg_match('#^/api/projects/(\d+)/doc$#', $path, $matches) === 1) {
    $apiController->projectDoc((int) $matches[1]);
    exit;
}

http_response_code(404);
echo '404 - No encontrado';
