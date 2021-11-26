<?php
// include_once("./Block.php");
include_once ('./TransactionInput.php');
include_once ('./TransactionOutput.php');
// include_once ('./Walet.php');
// include_once ('./StringUtil.php');
include_once ('./NoobChain.php');

class Transaction {
	
	public $transactionId; //Contains a hash of transaction*
	public $sender; //Senders address/public key.
	public $reciepient; //Recipients address/public key.
	public $value; //Contains the amount we wish to send to the recipient.
	public $signature; //This is to prevent anybody else from spending funds in our wallet.
	
	public $inputs=[];
	public $outputs=[];
	// public function __construct(){
	// 	$this->inputs[] = new TransactionInput;
	// 	$this->outputs[] = new TransactionOutput;
	// }
	
	private $sequence = 0; //A rough count of how many transactions have been generated
	
	// Constructor: 
	public function Transaction_( $from,  $to, $value,  $inputs) {
		
		$this->sender = $from;
		$this->reciepient = $to;
		$this->value = $value;
		$this->inputs = $inputs;

	}
	
	public function processTransaction() {
		
		if($this->verifySignature() == false) {
			echo "#Transaction Signature failed to verify";
			return false;
		}
				
		//Gathers transaction inputs (Making sure they are unspent):
		if(!empty($this->inputs)){
			foreach ($this->inputs as $key => $i) {
				echo "hiihihihi";
				var_dump($i);
				var_dump(NoobChain::$UTXOs);
				echo "hiihihihi";
				$i->UTXO = NoobChain::$UTXOs[$i->transactionOutputId];
				var_dump($i->UTXO);
			}
		}

		//Checks if transaction is valid:

		if($this->getInputsValue() < NoobChain::$minimumTransaction) {
			echo "Transaction Inputs too small: " . $this->getInputsValue();
			echo "Please enter the amount greater than " . NoobChain::$minimumTransaction;
			return false;
		}
		
		//Generate transaction outputs:
		$leftOver = $this->getInputsValue() - $this->value; //get value of inputs then the left over change:
		$this->transactionId = $this->calulateHash();
		$this->outputs[] = new TransactionOutput( $this->reciepient, $this->value, $this->transactionId); //send value to recipient
		$this->outputs[] = new TransactionOutput( $this->sender, $leftOver,$this->transactionId); //send the left over 'change' back to sender		
				
		//Add outputs to Unspent list
		foreach ($this->outputs as $key => $o) {
			NoobChain::$UTXOs[$o->id] = $o;
		}
		
		//Remove transaction inputs from UTXO lists as spent:
		foreach ($this->inputs as $key => $i) {
			$i->UTXO = NoobChain::$UTXOs[$i->transactionOutputId];
			var_dump($i->UTXO);
			if($i->UTXO == null) continue; //if Transaction can't be found skip it 
			NoobChain::$UTXOs[$i->UTXO->id];

			unset(NoobChain::$UTXOs[$i->UTXO->id]);

		}

		return true;
	}
	
	public function getInputsValue() {
	 	$total = 0;
	 	if(!empty($this->inputs)){
			foreach ($this->inputs as $key => $i) {
				if($i->UTXO == null) continue; //if Transaction can't be found skip it 
				$total += $i->UTXO->value;
			}
		}
		return $total;
	}
	
	public function generateSignature($privateKey) {
		$data = $this->sender . $this->reciepient . $this->value;
		$this->signature = StringUtil::applyECDSASig($privateKey,$data);		
	}
	
	public function verifySignature() {
		$data = $this->sender . $this->reciepient . $this->value;
		return StringUtil::verifyECDSASig($this->sender, $data, $this->signature);
	}
	
	public function getOutputsValue() {
		$total = 0;
		foreach ($this->outputs as $key => $o) {
			$total += $o->value;

		}
		return $total;
	}
	
	private function calulateHash() {
		$this->sequence++; //increase the sequence to avoid 2 identical transactions having the same hash
		$hash = hash('sha256', (string)$this->sender . (string)$this->reciepient . (string)$this->value . (string)$this->sequence);
		return $hash;
	}
}
