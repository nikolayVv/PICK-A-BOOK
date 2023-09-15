<?php

namespace app\core;

//abstract -> avoid creating instance of this class
abstract class Model {
    
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_DIGITS = 'digits';
    public const RULE_PHONE = 'phone';
    public const RULE_YEAR = 'year';
    
    public array $errors = [];

    public function loadData($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    } 

    //must be implemented in child class -> returns array
    abstract public function rules(): array;
    
    public function labels(): array {
        return [];
    }
    
    public function getLabel($attribute) {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function errorMessages() {
        return [
            self::RULE_REQUIRED => 'This field is required!',
            self::RULE_EMAIL => 'This field must be a valid email address!',
            self::RULE_MIN => 'Min length of this field must be {min}!',
            self::RULE_MAX => 'Max length of this field must be {max}!',
            self::RULE_MATCH => 'This field must be the same as {match}!',
            self::RULE_UNIQUE => 'Record with this {field} already exists!',
            self::RULE_DIGITS => 'This field must contain only digits!',
            self::RULE_PHONE => 'This field must have format ###-###-#### or ###-###-###!',
            self::RULE_YEAR => 'The field must have year smaller than {year}!'
        ];
    }

    private function addErrorForRule(string $attribute, string $rule, $params = []) {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            //we change the key with value in message
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message; 
    }
    
    public function addError(string $attribute, string $message) {
        $this->errors[$attribute][] = $message; 
    }

    public function validate() {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttribute=:attr");
                    $statement->bindParam(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
                if ($ruleName === self::RULE_DIGITS && !ctype_digit ($value)) {
                    $this->addErrorForRule($attribute, self::RULE_DIGITS);
                }
                if ($ruleName === self::RULE_PHONE && (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value) && !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{3}$/", $value))) {
                    if ($value !== '') {
                        $this->addErrorForRule($attribute, self::RULE_PHONE);
                    }
                }
                if ($ruleName === self::RULE_YEAR && intval($value, 10) > intval(date("Y"), 10)) {
                    $this->addErrorForRule($attribute, self::RULE_YEAR, $rule);
                }
            }
        }
        return empty($this->errors);
    }

    public function hasError($attribute) {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute) {
        return $this->errors[$attribute][0] ?? false;
    }
}