<?php

namespace app\controllers;
use app\core\Controller;
use app\core\Application;
use app\models\User;
use app\models\Post;
use app\models\Book;
use app\models\Order;
use app\models\BookOrder;
use app\core\Request;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\AuthorizedMiddleware;

class ProfileController extends Controller {
    public function __construct() {
        //restrict the access
        if (Application::$app->user) {
            if (Application::$app->user->uporabnik_tip == "stranka") {
                $this->registerMiddleware(new AuthorizedMiddleware(['pendingOrders', 'approvedOrders', 'allOrders', 'editOrder']));
            } else if (Application::$app->user->uporabnik_tip == "prodajalec") {
                $this->registerMiddleware(new AuthorizedMiddleware(['myOrders']));
            } else if (Application::$app->user->uporabnik_tip == "administrator") {
                $this->registerMiddleware(new AuthorizedMiddleware(['pendingOrders', 'approvedOrders', 'allOrders', 'myOrders', 'editOrder', 'viewOrder']));
            }   
        } else {
            $this->registerMiddleware(new AuthMiddleware(['editProfile', 'pendingOrders', 'approvedOrders', 'allOrders', 'myOrders', 'editOrder', 'viewOrder']));
        }
    }
    
    public function editProfile(Request $request, Response $response) {
        $userModel = new User();
        $user = $userModel->get(Application::$app->user->uporabnik_id);
        $this->setLayout('home');
        $postModel = new Post();
        $post = $postModel->findOne(['posta_stevilka' => $user->posta_stevilka]);
        $user->uporabnik_mesto = $post->posta_ime;
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $editedUser = new User();
            $changed = false;
            $editedUser->loadData($request->getBody());
            $editedUser->uporabnik_id = $user->uporabnik_id;
            $editedUser->uporabnik_tip = $user->uporabnik_tip;
            if (Application::$app->user->uporabnik_tip != 'stranka') {
                $editedUser->uporabnik_email = $user->uporabnik_email;
            } else {
                $editedUser->uporabnik_ime = $user->uporabnik_ime;
                $editedUser->uporabnik_priimek = $user->uporabnik_priimek;
            }
            if ($editedUser->uporabnik_geslo == '') {
                $editedUser->uporabnik_geslo = $user->uporabnik_geslo;
            }
            if ($editedUser->uporabnik_ime == '' || $editedUser->uporabnik_priimek == '' || $editedUser->uporabnik_mesto == '' || $editedUser->posta_stevilka == '' || !ctype_digit($editedUser->posta_stevilka) || strlen($editedUser->posta_stevilka) > 5 || strlen($editedUser->posta_stevilka) < 4 ||
                !filter_var($editedUser->uporabnik_email, FILTER_VALIDATE_EMAIL) || $editedUser->uporabnik_email == '' || strlen($editedUser->uporabnik_geslo) < 8 || (strlen($editedUser->uporabnik_geslo) > 20 && $changed == true) ||
                (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $editedUser->uporabnik_telefon) && !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/", $editedUser->uporabnik_telefon) && $editedUser->uporabnik_telefon != '')) {
                if ($editedUser->uporabnik_ime == '' ) {
                    $editedUser->addError('uporabnik_ime', 'This field is required!');
                }
                
                if ($editedUser->uporabnik_priimek == '') {
                    $editedUser->addError('uporabnik_priimek', 'This field is required!');
                }
                
                if ($editedUser->uporabnik_mesto == '') {
                    $editedUser->addError('uporabnik_mesto', 'This field is required!');
                }
                
                if ($editedUser->posta_stevilka == '') {
                    $editedUser->addError('posta_stevilka', 'This field is required!');
                } else if (!ctype_digit($editedUser->posta_stevilka)) {
                    $editedUser->addError('posta_stevilka', 'The ZIP must contain only digits!');
                } else if (strlen($editedUser->posta_stevilka) > 5 || strlen($editedUser->posta_stevilka) < 4) {
                    $editedUser->addError('posta_stevilka', 'The length of ZIP must be 4 or 5 digits!');
                }
                
                if ($editedUser->uporabnik_email == '') {
                    $editedUser->addError('uporabnik_email', 'This field is required!');
                } else if (!filter_var($editedUser->uporabnik_email, FILTER_VALIDATE_EMAIL)) {
                    $editedUser->addError('uporabnik_email', 'This field must be a valid email address!');
                }
                
                if (strlen($editedUser->uporabnik_geslo) < 8) {
                    $editedUser->addError('uporabnik_geslo', 'Min length of this field must be 8!');
                } else if (strlen($editedUser->uporabnik_geslo) > 20 && $changed == true) {
                    $editedUser->addError('uporabnik_geslo', 'Max length of this field must be 20!');
                }
                
                if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $editedUser->uporabnik_telefon) && !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/", $editedUser->uporabnik_telefon) && $editedUser->uporabnik_telefon != '') {
                    $editedUser->addError('uporabnik_telefon', 'This field must have format ###-###-#### or ###-###-###!');
                }
                
                $editedUser->uporabnik_geslo = '';
                return $this->render('editProfile', [
                    'model' => $editedUser
                ]);
            }
            if ($changed == true && Application::$app->user->uporabnik_tip == 'stranka') {
                $editedUser->uporabnik_geslo = password_hash($editedUser->uporabnik_geslo, PASSWORD_DEFAULT);
            }
            if ($editedUser->put()) {
                Application::$app->session->setFlash('success', 'Profile edited successful!');
                Application::$app->response->redirect("$rootPath/profile/editProfile");
            }
            return;
        }
        
        $user->uporabnik_geslo = '';
        return $this->render('editProfile', [
            'model' => $user
        ]);
    }
    
    public function pendingOrders(Request $request) {
        $ordersModel = new Order();
        $orders = $ordersModel->getAllOrders();
        $pendingOrders = [];
        foreach ($orders as $order) {
            if ($order["narocilo_status"] == "pending") {
                $pendingOrders[] = $order;
            }
        }
        
        $this->setLayout('home');
        return $this->render('orders', [
            'orders' => $pendingOrders,
            'statusLabel' => 'pending',
            'type' => ''
        ]);
    }
    
    public function approvedOrders(Request $request) {
        $ordersModel = new Order();
        $orders = $ordersModel->getAllOrders();
        $approvedOrders = [];
        foreach ($orders as $order) {
            if ($order["narocilo_status"] == "approved") {
                $approvedOrders[] = $order;
            }
        }
        
        $this->setLayout('home');
        return $this->render('orders', [
            'orders' => $approvedOrders,
            'statusLabel' => 'approved',
            'type' => ''
        ]);
    }
    
    public function allOrders(Request $request) {
        $ordersModel = new Order();
        $orders = $ordersModel->getAllOrders();
        
        $this->setLayout('home');
        return $this->render('orders', [
            'orders' => $orders,
            'statusLabel' => '',
            'type' => 'all'
        ]);
    }
    
    public function myOrders(Request $request) {
        $ordersModel = new Order();
        $orders = $ordersModel->getAllOrders();
        $myOrders = [];
        foreach ($orders as $order) {
            if (intval($order["uporabnik_id"], 10) == Application::$app->user->uporabnik_id) {
                $myOrders[] = $order;
            }
        }
        $this->setLayout('home');
        return $this->render('orders', [
            'orders' => $myOrders,
            'statusLabel' => '',
            'type' => 'my'
        ]);
    }
    
    public function viewOrder() {
        $bookOrderModel = new BookOrder();
        $bookorders = $bookOrderModel->getAllKnjigaNarocilo($_GET['id']);
        $bookModel = new Book();
        $i = 0;
        foreach ($bookorders as $bookorder) {
            $books[$i]['quantity'] =  $bookorder["knjiga_narocilo_kolicina"];
            $books[$i]['data'] =  $bookModel->getOneBook($bookorder["knjiga_id"]);
            $i++;
        }
        $total = 0;
        foreach ($books as $book) {
            $total += $book['quantity'] * $book['data']->knjiga_cena;
        }
        
        $index = $_GET['id'];
        return $this->render('order', [
            'books' => $books,
            'total' => $total,
            'index' => $index,
            'status' => 'edit',
            'prev' => $_GET['prev'] ?? '',
            'type' => $_GET['type'] ?? ''
        ]);
    }
    
    public function editOrder(Request $request, Response $response) {
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $orderModel = new Order();
            $order = $orderModel->getOrder($_GET['id']);
            $status = $_GET['status'];
            if ($status == "delete") {
                $order->narocilo_status = "approved and deleted";
                Application::$app->session->setFlash('success', 'Order status changed to "deleted" successful!');
                $order->updateOrder();
            } else if ($status == "accept") {
                $order->narocilo_status = "approved";
                Application::$app->session->setFlash('success', 'Order status changed to "approved" successful!');
                $order->updateOrder();
            } else if ($status == "reject") {
                $order->narocilo_status = "rejected";
                Application::$app->session->setFlash('success', 'Order status changed to "rejected" successful!');
                $order->updateOrder();
            }
            
            $prev = $_GET['prev'];
            if ($prev == "pending") {
                Application::$app->response->redirect("$rootPath/profile/pendingOrders");
            } else if ($prev == "approved") {
                Application::$app->response->redirect("$rootPath/profile/approvedOrders");
            }
            
            return;
        }
        
        $bookOrderModel = new BookOrder();
        $bookorders = $bookOrderModel->getAllKnjigaNarocilo($_GET['id']);
        $bookModel = new Book();
        $i = 0;
        foreach ($bookorders as $bookorder) {
            $books[$i]['quantity'] =  $bookorder["knjiga_narocilo_kolicina"];
            $books[$i]['data'] =  $bookModel->getOneBook($bookorder["knjiga_id"]);
            $i++;
        }
        $total = 0;
        foreach ($books as $book) {
            $total += $book['quantity'] * $book['data']->knjiga_cena;
        }
        
        $index = $_GET['id'];
        return $this->render('order', [
            'books' => $books,
            'total' => $total,
            'index' => $index,
            'status' => 'edit',
            'prev' => $_GET['prev'] ?? '',
            'type' => $_GET['type'] ?? ''
        ]);
    }
    
}
