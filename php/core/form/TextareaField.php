<?php

namespace app\core\form;

class TextareaField extends BaseField {
    public const NOTEDITABLE = false;
    public const EDITABLE = true;
   
    public bool $edit;
    
    public function notEditable() {
        $this->edit = self::NOTEDITABLE;
        return $this;
    }
    
    public function editable() {
        $this->edit = self::EDITABLE;
        return $this;
    }
    
    public function renderInput(): string {
        if (!$this->edit) {
            return sprintf('<textarea name="%s" class="form-control %s" disabled>%s</textarea>', 
                $this->attribute,
                $this->model->hasError($this->attribute) ? 'is-invalid' : '',
                $this->model->{$this->attribute}
            );
        }
        return sprintf('<textarea name="%s" class="form-control %s">%s</textarea>', 
            $this->attribute,
            $this->model->hasError($this->attribute) ? 'is-invalid' : '',
            $this->model->{$this->attribute}
        );
    }
}