<?php
class findPass {
	private $passArray;
	private $validCharsArray;
	private $charsToTry;
	private $numOfTries = 0 ;
	
	function __construct(){
		$this->charsToTry = array_merge(array (""),range('a','z'),range('A','Z'),range('0','9'));
	}
	public function startBlank(){
		$this->passArray = array();
		$this->validCharsArray = array();
		$this->buildPassword();
		echo "<BR><BR>\r\n\r\n";
	}
	public function startWithKnown($pA,$vCA){
		//$pA must be set with the key=>value syntax
		$this->passArray = $pA;
		$this->validCharsArray = $vCA;
		foreach($pA as $pAVal){
			unset($this->charsToTry[array_search($pAVal,$this->charsToTry)]);
		}
		foreach($vCA as $vCAVal){
			unset($this->charsToTry[array_search($vCAVal,$this->charsToTry)]);
		}
		$this->buildPassword();	
		echo "<BR><BR>\r\n\r\n";
	}
	private function buildPassword(){
		foreach($this->charsToTry as $cTTVal){
			$newCharUsed=0;
			$passPos=0;
			$passString="";
			ksort($this->passArray);
			foreach($this->passArray as $pAKey=>$pAVal){
				while($pAKey>$passPos){
					if ($newCharUsed==0){
						$passString=$passString.$cTTVal;
						$newCharUsed=1;
					} else {
						if(count($this->validCharsArray) > 0){
							$aPopped=array_pop($this->validCharsArray);
							$passString=$passString.$aPopped;
						}
					}
					$passPos++;
				}
 				if($pAKey==$passPos){
					$passString=$passString.$pAVal;
				}
				$passPos++;
			}
			if ($newCharUsed==0){
				$passString=$passString.$cTTVal;
				$newCharUsed=1;
			} 
			while (count($this->validCharsArray) > 0){
				$aPopped=array_pop($this->validCharsArray);
				$passString=$passString.$aPopped;
			}
			$this->numOfTries++;
			echo "Try number: $this->numOfTries - ";
			if($this->checkResult($passString)==1)return 1;
		}
	}
	private function checkResult($passString){
		$rArray = $this->getResults($passString);
		if($rArray==1)return 1;
		$pTempArray = str_split($passString);
		foreach($rArray as $rKey=>$rVal){
			$charVal=$pTempArray[$rKey];
			if($rVal=="11"){
				$this->passArray[$rKey]=$charVal;
			} elseif ($rVal=="01"){
				if(!is_array($this->validCharsArray)){
					$this->validCharsArray = array ($charVal);
				} else {
					$this->validCharsArray[]=$charVal;
				}
			} 
		}
	}
	private function getResults ($passString){
		Echo "Trying: $passString as the password <br>\n\r";
		$twUrL = "http://simple-snow-3171.herokuapp.com?password=$passString"; 
        $contents = file_get_contents($twUrL);
        if(preg_match('/Congratulations/', $contents)){
        	echo $contents;
        	echo "Number of tries: $this->numOfTries";
        	return 1;
        }
		$rArray=str_split(rtrim(ltrim(strip_tags($contents),'a..z,A..Z, ,:')),2);
        return $rArray;
	}
}
$findPassStartknown = new findPass();
echo "Starting with known chars<br>\n\r";
$findPassStartknown->startWithKnown(array("0"=>"Q"), array("9"));

$findPassStartnew = new findPass();
echo "Starting with a blank slate<br>\n\r";
$findPassStartnew->startBlank();

?>