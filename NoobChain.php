

<?php 
include_once("./Block.php");
// include_once ('./StringUtil.php');
// include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
include_once ('./TransactionOutput.php');
include_once ('./Walet.php');


class NoobChain {
	
	public static $blockchain = [];
	public static $UTXOs = [];
	
	public static $difficulty = 3;
	public static $minimumTransaction = 0.1;
	public $walletA;
	public $walletB;
	public $walletC;
	public static $genesisTransaction;

	public function __construct(){
		// $this->blockchain = new Block;
		// $this->UTXOs = new TransactionOutput;

	}

	public function NoobChain_() {
		//add our blocks to the blockchain ArrayList:
		// Security.addProvider(new org.bouncycastle.jce.provider.BouncyCastleProvider()); //Setup Bouncey castle as a Security Provider
		
		//Create wallets:
		// genarate keypair
		$this->walletA = new Wallet();
		$this->walletB = new Wallet();
		$this->walletC = new Wallet();
		$coinbase = new Wallet();
		
		//create genesis transaction, which sends 100 NoobCoin to walletA:
		self::$genesisTransaction = new Transaction;
		self::$genesisTransaction->Transaction_($coinbase->publicKey, $this->walletA->publicKey, 100, null);
		self::$genesisTransaction->generateSignature($coinbase->privateKey);	 //manually sign the genesis transaction	
		self::$genesisTransaction->transactionId = "0"; //manually set the transaction id
		$object_transout = new TransactionOutput;
		$object_transout->TransactionOutput_(self::$genesisTransaction->reciepient, self::$genesisTransaction->value, self::$genesisTransaction->transactionId);
		array_push(self::$genesisTransaction->outputs, $object_transout);

		// self::$genesisTransaction->outputs.add(new TransactionOutput(self::$genesisTransaction.reciepient, self::$genesisTransaction.value, self::$genesisTransaction.transactionId)); //manually add the Transactions Output
		self::$UTXOs[self::$genesisTransaction->outputs[0]->id] = self::$genesisTransaction->outputs[0]; //its important to store our first transaction in the UTXOs list.
		
		echo "Creating and Mining Genesis block... ";
		$genesis = new Block("0");
		$genesis->addTransaction(self::$genesisTransaction);
		self::addBlock($genesis);
		// var_dump(self::$blockchain);

		//testing
		$block1 = new Block($genesis->hash);
		echo "\nWalletA's balance is: " . $this->walletA->getBalance();
		echo "\nWalletA is Attempting to send funds (40) to WalletB...";
		$block1->addTransaction($this->walletA->sendFunds($this->walletB->publicKey, 40));
		self::addBlock($block1);

		echo "\nWalletA's balance is: " . $this->walletA->getBalance();
		echo "WalletB's balance is: " . $this->walletB->getBalance();
		// var_dump(self::$blockchain);
		// exit();


		$block2 = new Block($block1->hash);
		echo "\nWalletA Attempting to send more funds (1000) than it has...";
		$block2->addTransaction($this->walletA->sendFunds($this->walletB->publicKey, 1000));
		self::addBlock($block2);

		echo "\nWalletA's balance is: " . $this->walletA->getBalance();
		echo "WalletB's balance is: " . $this->walletB->getBalance();
		// var_dump(self::$blockchain);

		$block3 = new Block($block2->hash);
		echo "\nWalletB is Attempting to send funds (20) to WalletA...";
		$block3->addTransaction($this->walletB->sendFunds( $this->walletA->publicKey, 20));
		self::addBlock($block3);

		echo "\nWalletA's balance is: " . $this->walletA->getBalance();
		echo "WalletB's balance is: " . $this->walletB->getBalance();
		// var_dump(self::$blockchain);

		$block4 = new Block($block3->hash);
		echo "\nWalletB is Attempting to send funds (15) to WalletC...";
		$block4->addTransaction($this->walletB->sendFunds( $this->walletC->publicKey, 15));
		self::addBlock($block4);

		echo "\nWalletA's balance is: " . $this->walletA->getBalance();
		echo "WalletB's balance is: " . $this->walletB->getBalance();
		echo "WalletB's balance is: " . $this->walletC->getBalance();

		var_dump(self::$blockchain);
		self::isChainValid();
		
	}
	
	public static function isChainValid() {
		// $currentBlock;
		// $previousBlock;
		$hashTarget = '';
		for ($i=0; $i < self::$difficulty ; $i++) { 
			$hashTarget .= '0';
		}
		// $hashTarget = new String(new char[difficulty]).replace('\0', '0');
		// HashMap<String,TransactionOutput> tempUTXOs = new HashMap<String,TransactionOutput>(); //a temporary working list of unspent transactions at a given block state.

		// $tempUTXOs = new TransactionOutput();
		$tempUTXOs = [];
		$tempUTXOs[self::$genesisTransaction->outputs[0]->id] = self::$genesisTransaction->outputs[0] ;
		
		//loop through blockchain to check hashes:
		for ($i=1; $i < count(self::$blockchain); $i++) {
			$currentBlock = self::$blockchain[$i];
			$previousBlock = self::$blockchain[$i-1];

			// compare registered hash and calculated hash:
			if($currentBlock->hash != $currentBlock->CalculateHash() ){
				return false;
			}
			// compare previous hash and registered previous hash
			if($currentBlock->previousHash != $previousBlock->hash ){
				return false;
			}

			//check if hash is solved
			if(!(strpos(substr( $currentBlock->hash,  0, self::$difficulty), $hashTarget) !== false)){
				echo "#This block hasn't been mined";
				return false;
			}

			//loop thru blockchains transactions:
			for($t=0; $t <count($currentBlock->transactions); $t++) {
				$currentTransaction = $currentBlock->transactions[$t];
				
				if(!$currentTransaction->verifySignature()) {
					echo "#Signature on Transaction(" . $t . ") is Invalid";
					return false; 
				}
				if($currentTransaction->getInputsValue() != $currentTransaction->getOutputsValue()) {
					echo "#Inputs are note equal to outputs on Transaction(" . $t . ")";
					return false;
				}
				
				foreach ($currentTransaction->inputs as $key) {
					
					foreach ($tempUTXOs as $key1 => $value1) {
						if(isset($value1[$input->transactionOutputId])){
							$tempOutput = $value1[$input->transactionOutputId];
						}
					}
					// $tempOutput = $tempUTXOs[$input->transactionOutputId];
					
					if($tempOutput == null) {
						echo "#Referenced input on Transaction(" . $t . ") is Missing";
						return false;
					}
					
					if($input->UTXO->value != $tempOutput->value) {
						echo "#Referenced input Transaction(" . $t . ") value is Invalid";
						return false;
					}
					
					foreach ($tempUTXOs as $key2 => $value2) {
						if(isset($value2[$input->transactionOutputId])){
							unset($tempUTXOs[$key2]);
						}
					}
				}
				
				// for(TransactionOutput output: currentTransaction.outputs) {
				// 	tempUTXOs.put(output.id, output);
				// }

				foreach ($currentTransaction->outputs as $output) {
					$tempUTXOs[] = [$output->id=>$output];
				}
				
				if( $currentTransaction->outputs[0]->reciepient != $currentTransaction->reciepient) {
					echo "#Transaction(" . $t . ") output reciepient is not who it should be";
					return false;
				}
				if( $currentTransaction->outputs[1]->reciepient != $currentTransaction->sender) {
					echo "#Transaction(" . $t . ") output 'change' is not sender.";
					return false;
				}
				
			}
			
		}
		echo "Blockchain is valid";
		return true;
	}
	
	public static function addBlock($newBlock) {
		$newBlock->mineBlock(self::$difficulty);
		self::$blockchain[] = $newBlock;
	}
}

$check = new NoobChain();
$check->NoobChain_();


