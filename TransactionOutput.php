<?php 
// include_once("./Block.php");
// include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
// include_once ('./Walet.php');
// include_once ('./NoobChain.php');
include_once ('./StringUtil.php');

class TransactionOutput {

	public $id;
	public $reciepient; //also known as the new owner of these coins.
	public $value; //the amount of coins they own
	public $parentTransactionId; //the id of the transaction this output was created in

	public function TransactionOutput_($reciepient, $value, $parentTransactionId) {

		$this->reciepient = $reciepient;
		$this->value = $value;
		$this->parentTransactionId = $parentTransactionId;
		echo "xu ly transactionoutput ";
		$this->id = StringUtil::applySha256($reciepient . (string)$value . $parentTransactionId);

	}
	
	//Check if coin belongs to you
	public static function isMine($reciepient, $publicKey) {

		return ($publicKey == $reciepient);

	}
	
}

?>