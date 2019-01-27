<?php
require_once 'application/models/dao/Transactions.php';
require_once 'application/entities/Transaction.php';


class HomepageController extends Controller{
    
    
    public function run() {
        $transactions = new Transactions();
        $this->response->setAttribute('transactions', $transactions->get( date('Y'), date('n') ));
    }
}