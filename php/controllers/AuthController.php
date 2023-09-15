<?php

namespace app\controllers;
use app\core\Controller;
use app\core\Application;
use app\core\Request;
use app\core\Response;
use app\models\User;
use app\models\LoginForm;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\AuthorizedMiddleware;

class AuthController extends Controller {
    
    public function __construct() {
        //restrict the access
        if (Application::$app->user) {
            $this->registerMiddleware(new AuthorizedMiddleware(['register', 'login', 'cert'])); 
        } else {
            $this->registerMiddleware(new AuthMiddleware(['logout']));
        }
    }
    
    public function register(Request $request) {
        $userModel = new User();
        
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $userModel->loadData($request->getBody());
            if($userModel->validate() && $userModel->register()) {
                Application::$app->session->setFlash('success', 'Registration successful! You can login now!');
                Application::$app->response->redirect($rootPath);
                return;
            }
            return $this->render('register', [
                'model' => $userModel
            ]);
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $userModel
        ]);
    }

    public function login(Request $request, Response $response) {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                $rootPath = Application::$ROOT_DIR;
                $response->redirect("${rootPath}store?page=1");
                return;
            }
        }
        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm,
        ]);
    }
    
    public function cert(Request $request, Response $response) {
        if(!isset($_SERVER["HTTPS"])) {
            $url = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
            header("Location: " . $url);
            return;
        }
        $cert = filter_input(INPUT_SERVER, "SSL_CLIENT_CERT") ?? '';
        
        if ($cert != '') {
            $cert_data = openssl_x509_parse($cert);
        } else {
            Application::$app->session->setFlash('danger', 'You do not have any certificate! You can still login without certificate!');
            Application::$app->response->redirect(Application::$ROOT_DIR);
            return;
        }
        $userModel = new User();
        $cert_email = $cert_data['subject']['emailAddress'];
        $user = $userModel->findOne(['uporabnik_email' => $cert_email]);
        if ($request->isPost()) { 
            $loginForm = new LoginForm();
            $loginForm->loadData($request->getBody());
            if ($loginForm->uporabnik_geslo == $user->uporabnik_geslo) {
                if ($user->uporabnik_aktiviran == 0) {
                    Application::$app->session->setFlash('danger', 'This account was deactivated! Contact our team for more information!');
                    $response->redirect("${rootPath}cert");
                    return;
                } else {
                    if (Application::$app->login($user)) {
                        $rootPath = Application::$ROOT_DIR;
                        if ($user->uporabnik_tip == 'administrator') {
                            $response->redirect("${rootPath}store/allSellers");  
                        } else {
                            $response->redirect("${rootPath}store?page=1");  
                        }            
                        return;
                    } else {
                        Application::$app->session->setFlash('danger', 'Something went wrong!');
                        $user->uporabnik_geslo = '';
                        return $this->render('cert', [
                            'model' => $user
                        ]);;
                    }
                }
            } else {
                $user->addError('uporabnik_geslo', 'Password is incorrect!');
                $user->uporabnik_geslo = '';
                return $this->render('cert', [
                    'model' => $user
                ]);;
            }
        }
        $cert_role = $cert_data['subject']['CN'];
        if ($user && $user->uporabnik_tip == $cert_role) {
            $user->uporabnik_geslo = '';
            return $this->render('cert', [
                'model' => $user
            ]);;
        } else {
            Application::$app->session->setFlash('danger', 'The user with these credentials does not exist in the database!');
            Application::$app->response->redirect(Application::$ROOT_DIR);
            return;
        }
    }
    
    public function logout(Request $request, Response $response) {
        Application::$app->logout();
        $response->redirect(Application::$ROOT_DIR);
    }
}