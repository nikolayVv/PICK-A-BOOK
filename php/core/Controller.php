<?php 

namespace app\core;
use app\core\middlewares\BaseMiddleware;
use app\core\Application;

class Controller {

    public string $layout = 'auth';
    protected array $middlewares = [];
    public string $action = '';

    public function setLayout($layout) {
        $this->layout = $layout;
    }
    
    public function renderJSON($data, $httpResponseCode = 200) {
        header('Content-Type: application/json');
        http_response_code($httpResponseCode);
        return json_encode($data);
    }
    
    public function render($view, $params = []) {
        return Application::$app->view->renderView($view, $params);
    }
    
    public function registerMiddleware(BaseMiddleware $middleware) {
        $this->middlewares[] = $middleware;
    }
    
    public function getMiddlewares(): array {
        return $this->middlewares;
    }
}