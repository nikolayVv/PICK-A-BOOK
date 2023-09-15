<?php

namespace app\models;
use app\core\DbModel;

class BookOrder extends DbModel {
    public int $narocilo_id;
    public int $knjiga_id;
    public string $knjiga_narocilo_kolicina;
    
    public function add($orderId, $bookId, $total) {
        $this->narocilo_id = $orderId;
        $this->knjiga_id = $bookId;
        $this->knjiga_narocilo_kolicina = $total;
        return $this->save();
    }
    
    public function getAllKnjigaNarocilo($id) {
        return $this->getAllFiltered(['narocilo_id' => intval($id, 10)]);
    }
    
    public function deleteAllKnjigaNarocilo($id) {
        return $this->delete([ 'narocilo_id' => intval($id,10) ]);
    }
    
    public function attributes(): array {
        return ['narocilo_id', 'knjiga_id', 'knjiga_narocilo_kolicina'];
    }

    public function primaryKey(): string {
        return 'knjiga_id, narocilo_id';
    }

    public function rules(): array {
        
    }

    public function tableName(): string {
        return 'knjiga_narocilo';
    }

}