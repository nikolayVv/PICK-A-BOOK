<?php

namespace app\models;

use app\core\DbModel;
use app\core\Application;

class Cart extends DbModel {
    
    # Pridobi vse izdelke v kosarico. 
    public static function get() {
        $ids = Application::$app->session->getCart();
        if ($ids == []) {
            return [];
        }
        $i = 0;
        $bookModel = new Book();
        foreach($ids as $id) {
            $books[$i]["quantity"] = Application::$app->session->getQuantityCart($id);
            $books[$i]["data"] = $bookModel->getOneBook($id); 
            $i++;
        }
        return $books;
    }

    # Dodaj knjigo v kosarico. 
    public static function add($id) {
        Application::$app->session->setCart(strval($id));         
    }
    
    # Dodaj knjigo v kosarico. 
    public static function deleteFromCart($id) {
        Application::$app->session->unsetCart($id, false);         
    }
    
    # Dodaj knjigo v kosarico. 
    public static function deleteAll($id) {
        Application::$app->session->unsetCart($id, true);         
    }
        
    # Pocisti kosarico. 
    public static function purge() {
        Application::$app->session->remove('cart');
    }

    # Cena skupaj. 
    public static function total() {
        return array_reduce(self::getAll(), function ($total, $book) {
            return $total + $book["knjiga_cena"] * $book["quantity"];
        }, 0);
    }

    public function attributes(): array {
        
    }

    public function primaryKey(): string {
        
    }

    public function rules(): array {
        
    }

    public function tableName(): string {
        
    }

}

