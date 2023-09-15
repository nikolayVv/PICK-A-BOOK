<?php

namespace app\models;
use app\core\DbModel;

class Order extends DbModel {
    public int $narocilo_id;
    public float $narocilo_postavka;
    public string $narocilo_status;
    public int $uporabnik_id;
    
    # Pridobi narocilo z id_order.  
    public function getOrder($id) {
        return $this->findOne(['narocilo_id' => intval($id, 10)]);
    }
    
    # Pridobi vsa narocila.
    public function getAllOrders() {
        return $this->getAll();
    }

    public function deleteOrder() {
        $bookOrderModel = new BookOrder();
        $bookOrderModel->deleteAllKnjigaNarocilo($this->narocilo_id);
       
        return $this->delete(['narocilo_id' => intval($this->narocilo_id, 10)]);
    }
    
    # Ustvari narocilo. 
    public function createOrder($books, $id, $total) {
        $this->narocilo_status = "pending";
        $this->narocilo_postavka = $total;
        $this->uporabnik_id = $id;
        
        if ($this->save()) {
            $orderId = $this->getLastOrderId();
            
            $bookOrder = new BookOrder();
            foreach ($books as $book) {
                $bookOrder->add($orderId, $book['data']->knjiga_id, $book['quantity']);
            }
        }
    }
    
    public function getLastOrderId() {
        return $this->lastIndex();
    }
    
    # Posodobi status narocila. 
    public function updateOrder() {
        return $this->edit(['narocilo_id' => intval($this->narocilo_id, 10)]);
    }
    
    public function attributes(): array {
        return ['narocilo_postavka', 'narocilo_status', 'uporabnik_id'];
    }

    public function primaryKey(): string {
        return 'narocilo_id';
    }

    public function rules(): array {
        
    }

    public function tableName(): string {
        return 'narocilo';
    }

}