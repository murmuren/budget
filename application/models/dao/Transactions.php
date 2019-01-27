<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transactions
 *
 * @author oleggreen
 */
class Transactions {
    
    
    public function get($year, $month){
        
    }
    
    
    public function add($data){
        return DB::execute('
            INSERT INTO transactions (`name`, `category_id`, `amount`, `note`, `year`, `month`)
            VALUES (:name, :category_id, :amount, :note, :year, :month)',
            [
                ':name'=>$data['name'], 
                ':category_id'=>$data['category_id'],
                ':amount'=>$data['amount'],
                ':note'=>$data['note'],
                ':year'=>$data['year'],
                ':month'=>$data['month']
            ])->getInsertId();
    }
}
