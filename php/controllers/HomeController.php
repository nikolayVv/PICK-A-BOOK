<?php

namespace app\controllers;
use app\core\Controller;
use app\core\Application;
use app\core\Request;
use app\core\Response;
use app\models\Cart;
use app\models\Book;
use app\models\Order;
use app\models\User;
use app\models\Post;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\AuthorizedMiddleware;

class HomeController extends Controller {
    
    public function __construct() {
        //restrict the access
        if (Application::$app->user) {
            if (Application::$app->user->uporabnik_tip == "stranka") {
                $this->registerMiddleware(new AuthorizedMiddleware(['addBook', 'allCustomers', 'allSellers', 'editUser', 'addUser', 'changeStatus', 'editBook']));
            } else if (Application::$app->user->uporabnik_tip == "prodajalec") {
                $this->registerMiddleware(new AuthorizedMiddleware(['addInCart', 'makeOrder', 'cart', 'allSellers', 'bookDetails']));
            } else if (Application::$app->user->uporabnik_tip == "administrator") {
                $this->registerMiddleware(new AuthorizedMiddleware(['addBook', 'bookList', 'bookDetails', 'addInCart', 'makeOrder', 'cart', 'editBook']));
            }      
        } else {
            $this->registerMiddleware(new AuthMiddleware(['addBook', 'addInCart', 'editBook', 'makeOrder', 'cart', 'allCustomers', 'allSellers', 'editUser', 'addUser', 'changeStatus']));
        }
    }
    
    public function bookList(Request $request) {
        $this->setLayout('home');
        $bookModel = new Book();
        if (Application::$app->user && Application::$app->user->uporabnik_tip == "prodajalec") {
            $booksList = $bookModel->getAllBooks();
        } else {
            $booksList = $bookModel->getAllFiltered(['knjiga_aktiviran' => 1]);
        }
        
        return $this->render('book-list', [
            'books' => $booksList,
        ]);
    }
    
    public function addBook (Request $request) {
        $bookModel = new Book();
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $bookModel->loadData($request->getBody());
            $image = $_FILES['knjiga_slika'];
            $imageName = basename($_FILES['knjiga_slika']['name']);
            $imageType = $_FILES['knjiga_slika']['type'];
            if ($imageName == '') {
                $bookModel->knjiga_slika = "images/basicBookStoreBook.png";
                if($bookModel->validate() && $bookModel->addBook()) {
                    Application::$app->session->setFlash('success', 'Book added successful!');
                    Application::$app->response->redirect("${rootPath}store/addBook");
                    return;
                }
            } else {
                if ($imageType != 'image/jpeg' && $imageType != 'image/jpg' && $imageType != 'image/png') { 
                    $bookModel->addError('knjiga_slika', 'File must be .jpg, .png or .jpeg format!');
                } else {
                    $imagetemp = $_FILES['knjiga_slika']['tmp_name'];
                    $pathArray = explode('/', __DIR__);
                    $imagePath = "/" . $pathArray[1] . "/" . $pathArray[2] . "/" . $pathArray[3] . "/" . $pathArray[4] . "/images/";
                    if (is_uploaded_file($imagetemp)) {
                        if (copy($imagetemp, $imagePath . $imageName)) {
                            $bookModel->knjiga_slika = "images/" . $imageName;
                            if($bookModel->validate() && $bookModel->addBook()) {
                                Application::$app->session->setFlash('success', 'Book added successful!');
                                Application::$app->response->redirect("${rootPath}store/addBook");
                                return;
                            }
                        } else {
                            $bookModel->addError('knjiga_slika', 'Failed to move the photo!');
                        }
                    } else {
                        $bookModel->addError('knjiga_slika', 'Failed to upload the photo!');
                    }
                }
            }
            
            $this->setLayout('home');
            return $this->render('addBook', [
                'model' => $bookModel
            ]);
        }
        $this->setLayout('home');
        return $this->render('addBook', [
            'model' => $bookModel
        ]);
    }
    
    public function editBook(Request $request) {
        $bookModel = new Book();
        $rootPath = Application::$ROOT_DIR;
        $book = $bookModel->getOneBook($_GET['id']);
        if ($request->isPost()) {
            $bookModel->loadData($request->getBody());
            $bookModel->knjiga_id = $book->knjiga_id;
            $bookModel->knjiga_avtor = $book->knjiga_avtor;
            $bookModel->knjiga_naslov = $book->knjiga_naslov;
            $image = $_FILES['knjiga_slika'];
            $imageName = basename($_FILES['knjiga_slika']['name']);
            $imageType = $_FILES['knjiga_slika']['type'];
            if ($imageName == '') {
                $bookModel->knjiga_slika = $book->knjiga_slika;
                if($bookModel->validate()) {
                    $bookModel->edit(['knjiga_id' => $book->knjiga_id]);
                    Application::$app->session->setFlash('success', 'Book added successful!');
                    Application::$app->response->redirect("${rootPath}store?page=1");
                    return;
                }
            } else {
                if ($imageType != 'image/jpeg' && $imageType != 'image/jpg' && $imageType != 'image/png') { 
                    $bookModel->addError('knjiga_slika', 'File must be .jpg, .png or .jpeg format!');
                } else {
                    $imagetemp = $_FILES['knjiga_slika']['tmp_name'];
                    $pathArray = explode('/', __DIR__);
                    $imagePath = "/" . $pathArray[1] . "/" . $pathArray[2] . "/" . $pathArray[3] . "/" . $pathArray[4] . "/images/";
                    if (is_uploaded_file($imagetemp)) {
                        if (copy($imagetemp, $imagePath . $imageName)) {
                            $bookModel->knjiga_slika = "images/" . $imageName;
                            if($bookModel->validate()) {
                                $bookModel->edit(['knjiga_id' => $book->knjiga_id]);
                                Application::$app->session->setFlash('success', 'Book added successful!');
                                Application::$app->response->redirect("${rootPath}store?page=1");
                                return;
                            }
                        } else {
                            $bookModel->addError('knjiga_slika', 'Failed to move the photo!');
                        }
                    } else {
                        $bookModel->addError('knjiga_slika', 'Failed to upload the photo!');
                    }
                }
            }
            
            $this->setLayout('home');
            return $this->render('editBook', [
                'book' => $bookModel
            ]); 
        }
        $book = $bookModel->findOne(['knjiga_id' => intval($_GET['id'], 10)]);
        $this->setLayout('home');
        return $this->render('editBook', [
            'book' => $book
        ]);
    }
    
    public function bookDetails(Request $request) {
        $bookModel = new Book();
        $book = $bookModel->getOneBook($_GET['id']);
        $this->setLayout('home');
        return $this->render('bookDetails', [
            'book' => $book,
        ]);
    }
    
    public function addInCart(Request $request, Response $response) {
        $bookModel = new Book();
        $book = $bookModel->getOneBook($_GET['id']);
        $rootPath = Application::$ROOT_DIR;
        if ($book != null) {
            $cartModel = new Cart();
            $cart = $cartModel->add($book->knjiga_id);
            Application::$app->session->setFlash('success', "$book->knjiga_naslov by $book->knjiga_avtor added in cart successful!");
            Application::$app->response->redirect("${rootPath}store/cart?page=1");
            return;
        }
        Application::$app->response->redirect("${rootPath}store?page=1");
        return;
    }
    
    public function makeOrder(Request $request, Response $response) {
        $cartModel = new Cart();
        $books = $cartModel->get();
        $total = 0;
        foreach ($books as $book) {
            $total += $book['quantity'] * $book['data']->knjiga_cena;
        }
        $orderModel = new Order(); 
        
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $orderModel->createOrder($books, Application::$app->user->uporabnik_id, $total);
            $cartModel->purge();
            $this->setLayout('home');
            Application::$app->session->setFlash('success', 'Order is sent for pending successful!');
            Application::$app->response->redirect("${rootPath}store?page=1");
            return;
        }
        $index = $orderModel->getLastOrderId() + 1;
        return $this->render('order', [
            'books' => $books,
            'total' => $total,
            'index' => $index,
            'status' => 'confirm'
        ]);
    }
    
    public function cart(Request $request, Response $response) {
        $cartModel = new Cart();
        
        if ($request->isPost()) {
            $rootPath = Application::$ROOT_DIR;
            $bookModel = new Book();
            $book = $bookModel->getOneBook($_GET['id']);
            if ($_GET['action'] == "addOne") {
                $cart = $cartModel->add($book->knjiga_id);
                Application::$app->response->redirect("${rootPath}store/cart?page=1");
                return;
            }
            if ($_GET['action'] == "deleteOne") {
                $cart = $cartModel->deleteFromCart($book->knjiga_id);
                Application::$app->response->redirect("${rootPath}store/cart?page=1");
                return;
            }
            if ($_GET['action'] == "deleteAll") {
                $cart = $cartModel->deleteAll($book->knjiga_id);
                Application::$app->response->redirect("${rootPath}store/cart?page=1");
                return;
            }
            if ($_GET['action'] == "purge") {
                $cart = $cartModel->purge();
                Application::$app->response->redirect("${rootPath}store/cart?page=1");
                return;
            } 
        }
        $books = $cartModel->get();
        $total = 0;
        foreach ($books as $book) {
            $total += $book['quantity'] * $book['data']->knjiga_cena;
        }
        
        $this->setLayout('home');
        return $this->render('cart', [
            'books' => $books,
            'total' => $total
        ]);
    }
    
    public function allCustomers() {
        $userModel = new User();
        $users = $userModel->getAll();
        $customers = [];
        foreach ($users as $user) {
            if ($user["uporabnik_tip"] == "stranka") {
                $customers[] = $user;
            }
        }
        
        $this->setLayout('home');
        return $this->render('users', [
            'users' => $customers,
            'active' => 'customers',
            'type' => 'allCustomers'
        ]);
    }
    
    public function allSellers() {
        $userModel = new User();
        $users = $userModel->getAll();
        $sellers = [];
        foreach ($users as $user) {
            if ($user["uporabnik_tip"] == "prodajalec") {
                $sellers[] = $user;
            }
        }
        
        $this->setLayout('home');
        return $this->render('users', [
            'users' => $sellers,
            'active' => 'sellers',
            'type' => 'allSellers'
        ]);
    }
    
    public function editUser(Request $request) {
        $userModel = new User();
        $user = $userModel->get($_GET['id']);
        $this->setLayout('home');
        $postModel = new Post();
        $post = $postModel->findOne(['posta_stevilka' => $user->posta_stevilka]);
        $user->uporabnik_mesto = $post->posta_ime;
        $type = $_GET['type'] ?? '';
        $rootPath = Application::$ROOT_DIR;
        if ($request->isPost()) {
            $changed = false;
            $status = $_GET['status'] ?? '';
            if ($status == 'delete') {
                $prev = $_GET['prev'];
                $user->deleteUser();
                Application::$app->session->setFlash('success', 'Profile deleted successful!');
                Application::$app->response->redirect("${rootPath}store/$prev");
                return;
            } else {
                $editedUser = new User();
                $editedUser->loadData($request->getBody());
                $editedUser->uporabnik_id = $user->uporabnik_id;
                $editedUser->uporabnik_tip = $user->uporabnik_tip;
                if ($user->uporabnik_tip != 'stranka') {
                    $editedUser->uporabnik_ime = $user->uporabnik_ime;
                    $editedUser->uporabnik_priimek = $user->uporabnik_priimek;
                    $editedUser->uporabnik_email = $user->uporabnik_email;
                    $editedUser->uporabnik_geslo = $user->uporabnik_geslo;
                }
                if ($editedUser->uporabnik_geslo == '') {
                    $editedUser->uporabnik_geslo = $user->uporabnik_geslo;
                } else {
                    if ($user->uporabnik_tip == 'stranka') {
                        $changed = true; 
                    }
                }
                if ($editedUser->uporabnik_mesto == '' || $editedUser->posta_stevilka == '' || !ctype_digit($editedUser->posta_stevilka) || strlen($editedUser->posta_stevilka) > 5 || strlen($editedUser->posta_stevilka) < 4 || $editedUser->uporabnik_ime == '' || $editedUser->uporabnik_priimek == '' ||
                        strlen($editedUser->uporabnik_geslo) < 8 || (strlen($editedUser->uporabnik_geslo) > 20 && $changed == true) || (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $editedUser->uporabnik_telefon) && !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/", $editedUser->uporabnik_telefon) && $editedUser->uporabnik_telefon != '') ||
                        !filter_var($editedUser->uporabnik_email, FILTER_VALIDATE_EMAIL) || $editedUser->uporabnik_email == '') {
                    if ($editedUser->uporabnik_ime == '') {
                        $editedUser->addError('uporabnik_ime', 'This field is required!');
                    }
                    
                    if ($editedUser->uporabnik_priimek == '') {
                        $editedUser->addError('uporabnik_priimek', 'This field is required!');
                    }
                    
                    if ($editedUser->uporabnik_email == '') {
                        $editedUser->addError('uporabnik_email', 'This field is required!');
                    } else if (!filter_var($editedUser->uporabnik_email, FILTER_VALIDATE_EMAIL)) {
                        $editedUser->addError('uporabnik_email', 'This field must be a valid email address!');
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

                    if (strlen($editedUser->uporabnik_geslo) < 8) {
                        $editedUser->addError('uporabnik_geslo', 'Min length of this field must be 8!');
                    } else if (strlen($editedUser->uporabnik_geslo) > 20 && $changed == true) {
                        $editedUser->addError('uporabnik_geslo', 'Max length of this field must be 20!');
                    } else if ($changed == false) {
                        if ($user->uporabnik_tip == 'stranka') {
                            $editedUser->uporabnik_geslo = '';
                        }
                    }

                    if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $editedUser->uporabnik_telefon) && !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/", $editedUser->uporabnik_telefon) && $editedUser->uporabnik_telefon != '') {
                        $editedUser->addError('uporabnik_telefon', 'This field must have format ###-###-#### or ###-###-###!');
                    }

                    return $this->render('editUser', [
                        'ime' => "$user->uporabnik_ime $user->uporabnik_priimek",
                        'model' => $editedUser,
                        'prev' => $_GET['type'] ?? ''
                    ]);
                }
                if ($changed == true) {
                    $editedUser->uporabnik_geslo = password_hash($editedUser->uporabnik_geslo, PASSWORD_DEFAULT);
                }
                if ($editedUser->put()) {
                    Application::$app->session->setFlash('success', 'Profile edited successful!');
                }
            }
            Application::$app->response->redirect("${rootPath}store/$type");
            
            return;
        }
        
        if ($user->uporabnik_tip == 'stranka') {
            $user->uporabnik_geslo = '';
        }
        return $this->render('editUser', [
            'ime' => "$user->uporabnik_ime $user->uporabnik_priimek",
            'model' => $user,
            'prev' => $_GET['type'] ?? ''
        ]);
    }
    
    public function addUser(Request $request) {
        $userModel = new User();
        $rootPath = Application::$ROOT_DIR;
        if ($request->isPost()) {
            $role = $_GET['role'];
            $prev = $_GET['prev'];
            $userModel->loadData($request->getBody());
            if ($role == 'Seller') {
                $userModel->uporabnik_tip = 'prodajalec';
            }
            if($userModel->validate() && $userModel->register()) {
                Application::$app->session->setFlash('success', 'Registration successful! You can login now!');
                Application::$app->response->redirect("${rootPath}store/all$prev");
                return;
            }
            return $this->render('register', [
                'model' => $userModel
            ]);
        }
        $this->setLayout('home');
        return $this->render('addUser', [
            'model' => $userModel,
            'role' => $_GET['role'],
            'prev' => $_GET['prev']
        ]);
    }
    
    public function changeStatus() {
        $userModel = new User();
        $bookModel = new Book();
        $rootPath = Application::$ROOT_DIR;
        $type = $_GET['type'];
        $prev = $_GET['prev'] ?? '';
        if ($type == "activateUser") {
            $user = $userModel->get(intval($_GET['id'], 10));
            $user->uporabnik_aktiviran = 1;
            $message = 'User activated successful!';
            $user->put($user->uporabnik_geslo, "editUser");
        } else if ($type == "deactivateUser") {
            $user = $userModel->get(intval($_GET['id'], 10));
            $user->uporabnik_aktiviran = 0;
            $message = 'User deactivated successful!';
            $user->put($user->uporabnik_geslo, "editUser");
        } else if ($type == "activateBook") {
            $book = $bookModel->getOneBook(intval($_GET['id'], 10));
            $book->knjiga_aktiviran = 1;
            $message = 'Book activated successful!';
            $book->edit(['knjiga_id' => $book->knjiga_id]);
        } else if ($type == "deactivateBook") {
            $book = $bookModel->getOneBook(intval($_GET['id'], 10));
            $book->knjiga_aktiviran = 0;
            $message = 'Book deactivated successful!';
            $book->edit(['knjiga_id' => $book->knjiga_id]);
        }
        
        Application::$app->session->setFlash('success', $message);
        if ($type == "activateBook" || $type == "deactivateBook") {
            Application::$app->response->redirect("${rootPath}store?page=1");
            return;
        }
        Application::$app->response->redirect("${rootPath}store/$prev");
        return;
    }
}
