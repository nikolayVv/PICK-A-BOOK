<?php

namespace app\core;

class Session {
    
    protected const FLASH_KEY = 'flash_messages';
    protected const CART_KEY = 'cart';

    public function __construct() {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            //Mark to be removed at the end of the request
            $flashMessage['remove'] = true;
        }
        
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
    
    public function setFlash($key, $message) {
        $_SESSION[self::FLASH_KEY][$key] = [
            'removed' => false,
            'value' => $message
        ];
    }
    
    public function getFlash($key) {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }
    
    public function __destruct() {
        //Iterate over marked to be removed
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
                if ($flashMessage['remove'] != null) {
                    unset($flashMessages[$key]);
                }
        }
        
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
    
    public function setCart($key) {
        if (isset($_SESSION[self::CART_KEY][$key])) {
            $_SESSION[self::CART_KEY][$key] += 1;
        } else {
            $_SESSION[self::CART_KEY][$key] = 1;
        }
    }
    
    public function unsetCart($key, $all) {
        if ($_SESSION[self::CART_KEY][$key] == 1 || $all) {
            unset($_SESSION[self::CART_KEY][$key]);
        } else {
            $_SESSION[self::CART_KEY][$key] -= 1;
        }
    }
    
    public function getCart() {
        if (!isset($_SESSION[self::CART_KEY]) || empty($_SESSION[self::CART_KEY])) {
            return [];
        }

        return array_keys($_SESSION[self::CART_KEY]);
    }
    
    public function getQuantityCart($key) {
        return $_SESSION[self::CART_KEY][$key] ?? 0;
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key) {
       return $_SESSION[$key] ?? false;
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
}

