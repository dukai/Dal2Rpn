<?php

class ExpParser{
	static function isOperator($value){
		$operatorString = "+-*/()";
		return is_numeric(strpos($operatorString, $value));
	}
	
	static function getPrioraty($value){
		switch($value){
			case '+':
			case '-':
				return 1;
			case '*':
			case '/':
				return 2;
			default:
				return 0;
		}
	}
	
	static function prioraty($o1, $o2){
		return self::getPrioraty($o1) <= self::getPrioraty($o2);
	}
	
	static function dal2Rpn($exp){
		$inputStack = array();
		$outputStack = array();
		$outputQueue = array();
		$exp = preg_replace('/\s/', '', $exp);
		$cur = null;
		$prev = '';
		for($i = 0, $len = strlen($exp); $i < $len; $i++){
			$cur = $exp[$i];
			if(empty($prev)){
				
				
				if(self::isOperator($cur) || $cur == 'v'){
					$inputStack[] = $cur;
				}else{
					$prev .= $exp[$i];
				}
				
				
			}else{
				if(self::isOperator($cur) || $cur == 'v'){
					$inputStack[] = $prev;
					$inputStack[] = $cur;
					
					$prev = '';
				}else{
					$prev .= $cur;
				}
			}
		}
		if(!empty($prev)){
			$inputStack[] = $prev;
			$prev = '';
		}
		
		while (count($inputStack) > 0) {
			$cur = array_shift($inputStack);
			if(self::isOperator($cur)){
				if($cur == '('){
					$outputStack[] = $cur;
				}else if($cur == ')'){
					
					$po = array_pop($outputStack);
					while($po != '(' && count($outputStack) > 0){
						$outputQueue[] = $po;
						$po = array_pop($outputStack);
					}
					
					if($po != '('){
						throw new Exception("Unmatched barackets");
					}
				}else{
					while(self::prioraty($cur, end($outputStack)) && count($outputStack) > 0){
						$outputQueue[] = array_pop($outputStack);
					}
					$outputStack[] = $cur;
				}
			}else{
				if($cur != 'v'){
					$cur = floatval($cur);
				}
				$outputQueue[] = $cur;
			}
			
		}
		
		if(count($outputStack) > 0){
			if(end($outputStack) == ')' || end($outputQueue) == '('){
				throw new Exception("Unmatched barackets");
			}
			
			while(count($outputStack) > 0){
				$outputQueue[] = array_pop($outputStack);
			}
		}
		
		return $outputQueue;
	}

	static function getResult($p1, $p2, $o){
		switch($o){
			case '+':
				return $p1 + $p2;
			case '-':
				return $p1 - $p2;
			case '*':
				return $p1 * $p2;
			case '/':
				return $p1 / $p2;
			default:
				throw new Exception("unknow operaor");
				break;
		}
	}
	
	static function evalRpn($rpnQueue, $value = 0){
		$outputStack = array();
		while(count($rpnQueue) > 0){
			$cur = array_shift($rpnQueue);
			
			if(!self::isOperator($cur)){
				$outputStack[] = $cur;
			}else{
				if(count($outputStack) < 2){
					throw new Exception("unvalid stack length");
				}
				$sec = array_pop($outputStack);
				$fir = array_pop($outputStack);
				
				if($sec == 'v'){
					$sec = $value;
				}
				
				if($fir == 'v'){
					$fir = $value;
				}
				
				$outputStack[] = self::getResult($fir, $sec, $cur);
			}
		}
		
		if(count($outputStack) != 1){
			throw new Exception("unvalid expression");
		}else{
			return $outputStack[0];
		}
	}
}

