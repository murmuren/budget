<?php
require_once 'application/models/dao/Transactions.php';


/**
 * Description of TransactionAddController
 *
 * @author oleggreen
 */
class TransactionAddController extends Controller{
    
    
    public function run() {
        $transactions = new Transactions();
        $this->response->setAttribute('answer', $transactions->add($_POST['data']));
    }
}
