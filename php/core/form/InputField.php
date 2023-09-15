<?php

namespace app\core\form;
use app\core\Model;

class InputField extends BaseField {
    
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_FILE = 'file';
    public const NOTEDITABLE = false;
    
    public string $type;
    public bool $edit;

    public function __construct(Model $model, string $attribute) {
        $this->type = self::TYPE_TEXT;
        $this->edit = true;
        parent::__construct($model, $attribute);
    }

    public function passwordField() {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }
    
    public function numberField() {
        $this->type = self::TYPE_NUMBER;
        return $this;
    }

    public function fileField() {
        $this->type = self::TYPE_FILE;
        return $this;
    }
    
    public function notEditable() {
        $this->edit = self::NOTEDITABLE;
        return $this;
    }


    public function renderInput(): string {
        if (!$this->edit) {
            return sprintf('<input name="%s" type="%s" value="%s" class="form-control %s" placeholder="%s" disabled/>', 
                $this->attribute,
                $this->type,
                $this->model->{$this->attribute},
                $this->model->hasError($this->attribute) ? 'is-invalid' : '',
                $this->model->getLabel($this->attribute)
            );
        }
        if ($this->type == "number") {
            return sprintf('<input name="%s" type="%s" value="%s" class="form-control %s" placeholder="%s" step="0.01" min="0"/>', 
                $this->attribute,
                $this->type,
                $this->model->{$this->attribute},
                $this->model->hasError($this->attribute) ? 'is-invalid' : '',
                $this->model->getLabel($this->attribute)
            );
        }
        if ($this->type == "file") {
            return sprintf('<input name="%s" type="%s" value="%s" class="form-control %s" placeholder="%s" accept="image/png, image/jpg, image/jpeg"/>', 
                $this->attribute,
                $this->type,
                $this->model->{$this->attribute},
                $this->model->hasError($this->attribute) ? 'is-invalid' : '',
                $this->model->getLabel($this->attribute)
            );
        }
        return sprintf('<input name="%s" type="%s" value="%s" class="form-control %s" placeholder="%s"/>', 
            $this->attribute,
            $this->type,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->model->getLabel($this->attribute)
        );
    }
}