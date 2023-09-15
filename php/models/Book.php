<?php

namespace app\models;
use app\core\DbModel;

class Book extends DbModel {
    
    public int $knjiga_id;
    public string $knjiga_avtor='';
    public string $knjiga_naslov='';
    public float $knjiga_cena=0.0;
    public string $knjiga_leto='';
    public string $knjiga_opis='';
    public string $knjiga_slika='';
    public int $knjiga_aktiviran=1;


    public function getAllBooks() {
        return $this->getAll();
    }
    
    public function getOneBook($id) {
        return $this->findOne(['knjiga_id' => intval($id, 10)]);
    }
    
    public function addBook() {
        return $this->save();
    }
    
    public function deleteBook() {
        return $this->delete(['knjiga_id' => $this->knjiga_id]);
    }
    
    public function attributes(): array {
        return ['knjiga_avtor', 'knjiga_naslov', 'knjiga_cena', 'knjiga_leto', 'knjiga_slika', 'knjiga_opis', 'knjiga_aktiviran'];
    }

    public function primaryKey(): string {
        return 'knjiga_id';
    }

    public function rules(): array {
         return [
            'knjiga_avtor' => [self::RULE_REQUIRED],
            'knjiga_naslov' => [self::RULE_REQUIRED],
            'knjiga_cena' => [self::RULE_REQUIRED],
            'knjiga_leto' => [self::RULE_REQUIRED, self::RULE_DIGITS, [self::RULE_MIN, 'min' => 4], [self::RULE_MAX, 'max' => 4], [self::RULE_YEAR, 'year' => date("Y")]],
        ];
    }
    
    public function labels(): array {
        return [
            'knjiga_avtor' => 'Author',
            'knjiga_naslov' => 'Title',
            'knjiga_cena' => 'Price (€)',
            'knjiga_leto' => 'Year Of Publishing',
            'knjiga_slika' => 'Photo',
            'knjiga_opis' => 'Description'
        ];
    }

    public function tableName(): string {
        return 'knjiga';
    }

}