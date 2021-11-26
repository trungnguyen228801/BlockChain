<?php 
// include_once ('./NoobChain.php');
// include_once ('./StringUtil.php');
include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
// include_once ('./TransactionOutput.php');
// include_once ('./Walet.php');

class Block {
	public $hash;
	public $previousHash;
	public $merkleRoot;
	public $transactions = [];
	public $timeStamp;
	public $nonce;
	// public function __construct(){
	// }

	public function Block($previousHash){
		// $this->transactions = new Transaction;
		$this->previousHash = $previousHash;
		$this->timeStamp = (new DateTime())->getTimestamp();
		$this->hash = $this->CalculateHash();
	}

	public function CalculateHash(){
		$calculatedhash = hash('sha256', $this->previousHash . (string)$this->timeStamp. (string)$this->nonce . (string)$this->merkleRoot);
		return $calculatedhash;
	}

	public function mineBlock( $difficulty ){
		//Create a string with difficulty * "0" 
		$this->merkleRoot = StringUtil::getMerkleRoot($this->transactions);

		$target = '';
		for ($i=0; $i < $difficulty ; $i++) { 
			$target .="0";
		}

		while (substr($this->hash, 0, $difficulty) !== $target) {
			$this->nonce ++;
			$this->hash = $this->CalculateHash();
		}

		echo "Block Mined!!! : " . $this->hash."        target:".$target."\n";

	}

	//Add transactions to this block
	public function addTransaction( $transaction ){
		
		//process transaction and check if valid, unless block is genesis block then ignore.
		
		if($transaction == null) return false;
		
		if(!($this->previousHash == '0')){
			if($transaction->processTransaction() != true){
				echo "Transaction failed to process. Discarded.";
				return false;
			}
		}

		$this->transactions[] = $transaction;

		echo "Transaction Successfully added to Block";
		return true;
	}

}

?>