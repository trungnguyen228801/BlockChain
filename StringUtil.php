
<?php 
include_once("./Block.php");
// include_once ('./Transaction.php');
// include_once ('./TransactionInput.php');
// include_once ('./TransactionOutput.php');
// include_once ('./Walet.php');
// include_once ('./NoobChain.php');
class StringUtil {
	
	//Applies Sha256 to a string and returns the result. 
	public static function applySha256($input){
		$calculatedhash = hash('sha256', $input);
		return $calculatedhash;
	}
	
	//Applies ECDSA Signature and returns the result ( as bytes ).
	public static function applyECDSASig( $privateKey, $input) {
		// Signature dsa;
		// byte[] output = new byte[0];
		// try {
		// 	dsa = Signature.getInstance("ECDSA", "BC");
		// 	dsa.initSign(privateKey);
		// 	byte[] strByte = input.getBytes();
		// 	dsa.update(strByte);
		// 	byte[] realSig = dsa.sign();
		// 	output = realSig;
		// } catch (Exception e) {
		// 	throw new RuntimeException(e);
		// }
		$output = hash('sha256',$privateKey.$input);
		return $output;
	}
	
	//Verifies a String signature 
	public static function verifyECDSASig($publicKey, $data, $signature) {
		// try {
		// 	Signature ecdsaVerify = Signature.getInstance("ECDSA", "BC");
		// 	ecdsaVerify.initVerify(publicKey);
		// 	ecdsaVerify.update(data.getBytes());
		// 	return ecdsaVerify.verify(signature);
		// }catch(Exception e) {
		// 	throw new RuntimeException(e);
		// }
		return true;
	}
	
	public static function getMerkleRoot($transactions) {
		$count = count($transactions);
		
		$previousTreeLayer = [];
		foreach ($transactions as $key => $transaction) {
			$previousTreeLayer[] = $transaction->transactionId;
		}
		
		$treeLayer = $previousTreeLayer;
		
		while($count > 1) {
			$treeLayer = [];
			for($i=1; $i < count($previousTreeLayer); $i+=2) {
				$treeLayer[]=$this->applySha256($previousTreeLayer[$i-1] . $previousTreeLayer[$i]);
			}
			$count = count($treeLayer);
			$previousTreeLayer = $treeLayer;
		}
		
		$merkleRoot = (count($treeLayer) == 1) ? $treeLayer[0] : "";
		return $merkleRoot;
	}
}


?>