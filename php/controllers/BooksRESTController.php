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
use app\core\View;

class BooksRESTController extends Controller { 
    
    public function bookList() {
        $id = $_GET['id'] ?? '';
        $bookModel = new Book();
        if ($id !== '') {
            $book = $bookModel->getOneBook($id);
            echo $this->renderJSON($book);
        } else {
            $booksList = $bookModel->getAllFiltered(['knjiga_aktiviran' => 1]);
            echo $this->renderJSON($booksList);
        }
    }
    
    
}