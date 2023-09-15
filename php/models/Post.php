<?php

namespace app\models;
use app\core\DbModel;

class Post extends DbModel {

    public string $posta_stevilka = '';
    public string $posta_ime = '';
    
    public function tableName(): string {
        return 'posta';
    }
    
    public function deletePost($id) {
        return $this->delete(['posta_stevilka' => intval($id, 10)]);
    }
    
    public function verify($posta_ime, $posta_stevilka) {
        $post = $this->findOne(['posta_stevilka' => $posta_stevilka]);
        if ($post == false) {
            $this->posta_stevilka = $posta_stevilka;
            $this->posta_ime = $posta_ime;
            return $this->save();
        }
        return true;
    }
    
    public function attributes(): array {
        return ['posta_stevilka', 'posta_ime'];
    }
    
    public function rules(): array {
        return [];
    }

    public function primaryKey(): string {
        return 'posta_stevilka';
    }

}