<?php 
// include_once("./Block.php");
// include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
// include_once ('./TransactionOutput.php');
// include_once ('./StringUtil.php');
// include_once ('./NoobChain.php');

class Wallet {
	
	public $privateKey;
	public $publicKey;
	
	private $UTXOs = [];
	public function __construct(){
		// $this->UTXOs= new TransactionOutput;
		$this->generateKeyPair();
	}
	public function Wallet_() {
	}

	public function generate_string($input, $strength = 16) {
	    $input_length = strlen($input);
	    $random_string = '';
	    for($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }
	 
	    return $random_string;
	}
		
	public function generateKeyPair() {
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->privateKey = $this->generate_string($permitted_chars, 40);
        $this->publicKey = $this->generate_string($permitted_chars, 40);

	}
	
	public function getBalance() {
		$total = 0;
		foreach (NoobChain::$UTXOs as $key => $item) {
			
			if(TransactionOutput::isMine($item->reciepient,$this->publicKey)){
				$this->UTXOs[$item->id]=$item;
				$total += (integer)$item->value;
			}
		}

		return $total;

	}
	
	public function sendFunds($_recipient,$value ) {
		if($this->getBalance() < $value) {
			echo "#Not Enough funds to send transaction. Transaction Discarded.";
			return null;
		}

		$inputs = [];
		
		$total = 0;

		foreach ($this->UTXOs as $key => $item) {
			$total += $item->value;
			$inputs[] = new TransactionInput($item->id);
			if($total > $value) break;

		}
		
		$newTransaction = new Transaction;
		$newTransaction->Transaction_($this->publicKey, $_recipient , $value, $inputs);
		$newTransaction->generateSignature($this->privateKey);
		echo "okokook\n";
		var_dump($newTransaction);
		// var_dump($this->UTXOs);
		echo "okokook\n";

		foreach ($inputs as $key => $input) {
			unset($this->UTXOs->input->TransactionOutputId);
		}
		
		return $newTransaction;
	}
	
}


