<?php

namespace app\core\form;
use app\core\Model;

class Form {

    public static function begin($action, $method) {
        echo sprintf('<form enctype="multipart/form-data" action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end() {
        echo '</form>';
    }

    public function field(Model $model, $attribute) {
        return new InputField($model, $attribute);
    }
    
    public function textarea(Model $model, $attribute) {
        return new TextareaField($model, $attribute);
    }
}