<?php 
// include_once("./Block.php");
// include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
// include_once ('./TransactionOutput.php');
// include_once ('./Walet.php');
// include_once ('./NoobChain.php');

class TransactionInput {
	public $transactionOutputId; //Reference to TransactionOutputs -> transactionId
	public $UTXO; //Contains the Unspent transaction output
	
	public function TransactionInput($transactionOutputId) {
		$this->transactionOutputId = $transactionOutputId;
	}
}

?>