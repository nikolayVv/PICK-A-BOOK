<?php

namespace app\core;
use app\core\View;

class Application {
    
    public static string $ROOT_DIR;
    
    public string $layout = 'auth';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public static Application $app;
    public ?Controller $controller = null;
    public Session $session;
    public Database $db;
    public ?DbModel $user;
    public View $view;

    public function __construct($rootPath, array $config) {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->userClass = $config['userClass'];
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        
        $this->db = new Database($config['db']);
        
        $primaryValue = $this->session->get('user');
        //preverimo ce ni anonimen
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
        
    }

    public function run() {
        try {
            echo $this->router->resolve();
        } catch (\Exception $ex) {
            $this->response->setStatusCode($ex->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $ex
            ]);
        }
        
    }

    public function getController() {
        return $this->controller;
    }

    public function setController(Controller $controller) {
        $this->controller = $controller;
    }
    
    public function login(DbModel $user) {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }
    
    public function logout() {
        $this->user = null;
        $this->session->remove('user');
        $this->session->remove('cart');
    }
    
    public static function isGuest() {
        return !self::$app->user;
    }
}