<?php

namespace app\core;

abstract class DbModel extends Model {
    
    abstract public function tableName(): string;
    abstract public function attributes(): array;
    abstract public function primaryKey(): string;

    public function save() {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (".implode(',', $attributes).") "
                        ."VALUES(".implode(',', $params).")"); 
        foreach ($attributes as $attribute) {
            $statement->bindParam(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }
    
    public function edit($where) {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = implode(", ", array_map(fn($attr) => "$attr=:$attr", $attributes));
        $sql = implode(" AND ", array_map(fn($attr) => "$attr=:$attr", array_keys($where)));
        $statement = self::prepare("UPDATE $tableName SET $params WHERE $sql"); 
        foreach ($attributes as $attribute) {
            $statement->bindParam(":$attribute", $this->{$attribute});
        }
        foreach ($where as $key => $item) {
            $statement->bindParam(":$key", $item);
        }
        $statement->execute();
        return true;
    }
    
    public function delete($where) {
        $tableName = $this->tableName();
        $sql = implode(" AND ", array_map(fn($attr) => "$attr=:$attr", array_keys($where)));
        $statement = self::prepare("DELETE FROM $tableName WHERE $sql"); 
        foreach ($where as $key => $item) {
            $statement->bindParam(":$key", $item);
        }
        $statement->execute();
        return true;
    }
    
    public function getAll() {
        $tableName = $this->tableName();
        $statement = self::prepare("SELECT * FROM $tableName"); 
        $statement->execute();
        return $statement->fetchAll();
    }
    
    public function getAllFiltered($where) {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr=:$attr", $attributes));
        
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindParam(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
    
    
    public function findOne($where) {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr=:$attr", $attributes));
        
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindParam(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }
    
    public function prepare($sql) {
        return Application::$app->db->pdo->prepare($sql);;
    }
    
    public function lastIndex() {
        return Application::$app->db->pdo->lastInsertId();
    }
}
