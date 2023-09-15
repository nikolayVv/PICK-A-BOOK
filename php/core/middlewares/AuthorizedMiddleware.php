<?php

namespace app\core\middlewares;
use \app\core\Application;
use app\core\exception\ForbiddenException;

class AuthorizedMiddleware extends BaseMiddleware {
    
    public array $actions = [];
    
    public function __construct(array $actions) {
        $this->actions = $actions;
    }
    
    public function execute() {
        if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
            if (Application::$app->user->uporabnik_tip !== 'administrator') {
                Application::$app->response->redirect(Application::$ROOT_DIR . 'store?page=1');
            } else {
                Application::$app->response->redirect(Application::$ROOT_DIR . 'store/allSellers');
            }
            
        }
    }

}