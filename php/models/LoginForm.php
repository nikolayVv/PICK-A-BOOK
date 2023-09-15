<?php

namespace app\models;

use app\core\Application;
use app\core\DbModel;

class LoginForm extends DbModel {
    
    public string $uporabnik_email = '';
    public string $uporabnik_geslo = '';
    
    public function rules(): array {
        return [
            'uporabnik_email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'uporabnik_geslo' => [self::RULE_REQUIRED]
        ];
    }
    
    public function labels(): array {
        return [
            'uporabnik_email' => 'Email',
            'uporabnik_geslo' => 'Password'
        ];
    }
    
    public function login() {
        $user = User::findOne(['uporabnik_email' => $this->uporabnik_email]);
        if (!$user) {
            $this->addError('uporabnik_email', 'User with this email address does not exist');
            return false;
        }
        if (!password_verify($this->uporabnik_geslo, $user->uporabnik_geslo)) {
            $this->addError('uporabnik_geslo', 'Password is incorrect');
            return false;
        }
        if ($user->uporabnik_aktiviran == 0) {
            Application::$app->session->setFlash('danger', 'This account was deactivated! Contact our team for more information!');
            Application::$app->response->redirect(Application::$ROOT_DIR);
            return false;
        }
        
        return Application::$app->login($user);
        
    }

    public function attributes(): array {
        return [];
    }

    public function attributesDb(): array {
        return [];
    }

    public function tableName(): string {
        return '';
    }

    public function primaryKey(): string {
        return 'uporabnik_id';
    }

}

