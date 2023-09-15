<?php

namespace app\models;

use app\core\UserModel;
use Spatie\Async\Pool;

class User extends UserModel {
    
    
    public int $uporabnik_id;
    public string $uporabnik_tip = 'stranka';
    public string $uporabnik_ime = '';
    public string $uporabnik_priimek = '';
    public string $uporabnik_email = '';
    public string $uporabnik_geslo = '';
    public string $confirmPassword = '';
    public string $uporabnik_naslov = '';
    public string $posta_stevilka = '';
    public string $uporabnik_telefon = '';
    public string $uporabnik_mesto = '';
    public int $uporabnik_aktiviran = 1;


    public function tableName(): string {
        return 'uporabnik';
    }
    
    public function get($id) {
        return $this->findOne(['uporabnik_id' => intval($id, 10)]);
    }
    
    public function deleteUser() {
        return $this->delete(['uporabnik_id' => intval($this->uporabnik_id, 10)]);
    }
    
    public function put() {
        $postModel = new Post();
        $result = $postModel->verify($this->uporabnik_mesto, $this->posta_stevilka);
        return $this->edit(['uporabnik_id' => intval($this->uporabnik_id, 10)]);
    }
    
    public function attributes(): array {
        $attributes = ['uporabnik_ime', 'uporabnik_priimek', 'uporabnik_email', 'uporabnik_geslo', 'uporabnik_aktiviran', 'uporabnik_naslov', 'posta_stevilka', 'uporabnik_telefon', 'uporabnik_tip'];
        
        return $attributes;
    }

    public function register() {
        $postModel = new Post();
        $result = $postModel->verify($this->uporabnik_mesto, $this->posta_stevilka);
        
        if ($this->uporabnik_tip == 'stranka') {
            $this->uporabnik_geslo = password_hash($this->uporabnik_geslo, PASSWORD_DEFAULT);
        }
        return $this->save();
    }
    
    public function labels(): array {
        return [
            'uporabnik_ime' => 'First name',
            'uporabnik_priimek' => 'Last name',
            'uporabnik_email' => 'Email',
            'uporabnik_geslo' => 'Password',
            'confirmPassword' => 'Confirm password',
            'uporabnik_naslov' => 'Street',
            'uporabnik_mesto' => 'City',
            'posta_stevilka' => 'ZIP',
            'uporabnik_telefon' => 'Phone number '
        ];
    }

    public function rules(): array {
        return [
            'uporabnik_ime' => [self::RULE_REQUIRED],
            'uporabnik_priimek' => [self::RULE_REQUIRED],
            'uporabnik_email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class, 'attribute' => 'uporabnik_email']],
            'uporabnik_geslo' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'uporabnik_geslo']],
            'uporabnik_mesto' => [self::RULE_REQUIRED],
            'posta_stevilka' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 4], [self::RULE_MAX, 'max' => 5], self::RULE_DIGITS],
            'uporabnik_telefon' => [self::RULE_PHONE]
        ];
    }

    public function attributesDb(): array {
        
    }

    public function primaryKey(): string {
        return 'uporabnik_id';
    }

    public function getDisplayName(): string {
        return $this->uporabnik_ime . ' ' . $this->uporabnik_priimek;
    }
}