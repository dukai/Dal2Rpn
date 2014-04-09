function isOperator(value){
	var operatorString = "+-*/()";
	return operatorString.indexOf(value) > -1
}

function getPrioraty(value){
	switch(value){
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

function prioraty(o1, o2){
	return getPrioraty(o1) <= getPrioraty(o2);
}

function dal2Rpn(exp){
	var inputStack = [];
	var outputStack = [];
	var outputQueue = [];
	exp = exp.replace(/\s/g, '');
	var cur = '', prev = '';
	for(var i = 0, len = exp.length; i < len; i++){
		var cur = exp[i];
		if(prev = ''){
			if(isOperator(cur) || cur = 'v'){
				inputStack.push(cur);
			}else{
				prev += cur;
			}
			
		}else{
			if(isOperator(cur) || cur = 'v'){
				inputStack.push(prev);
				inputStack.push(cur);
				prev = '';
			}else{
				prev += cur;
			}
		}
	}
	while(inputStack.length > 0){
		var cur = inputStack.shift();
		if(isOperator(cur)){
			if(cur == '('){
				outputStack.push(cur);
			}else if(cur == ')'){
				var po = outputStack.pop();
				while(po != '(' && outputStack.length > 0){
					outputQueue.push(po);
					po = outputStack.pop();
				}
				if(po != '('){
					throw "error: unmatched ()";
				}
			}else{
				while(prioraty(cur, outputStack[outputStack.length - 1]) && outputStack.length > 0){
					outputQueue.push(outputStack.pop());
				}
				outputStack.push(cur);
			}
		}else{
			outputQueue.push(new Number(cur));
		}
	}
	if(outputStack.length > 0){
		if(outputStack[outputStack.length - 1] == ')' || outputStack[outputStack.length - 1] == '('){
			throw "error: unmatched ()";
		}
		while(outputStack.length > 0){
			outputQueue.push(outputStack.pop());
		}
	}
	return outputQueue;

}

function evalRpn(rpnQueue){
	var outputStack = [];
	while(rpnQueue.length > 0){
		var cur = rpnQueue.shift();

		if(!isOperator(cur)){
			outputStack.push(cur);
		}else{
			if(outputStack.length < 2){
				throw "unvalid stack length";
			}
			var sec = outputStack.pop();
			var fir = outputStack.pop();

			outputStack.push(getResult(fir, sec, cur));
		}
	}

	if(outputStack.length != 1){
		throw "unvalid expression";
	}else{
		return outputStack[0];
	}
}
