var invConds;
addEventListener("DOMContentLoaded", function() {
	var secs = ["Social Participation", "Vision", "Hearing", "Touch",
		"Body Awareness", "Balance and Motion", "Planning"];
	var percentileKeys = Object.keys(raPercentilesSPM[8]);
	var resultsBySection = [[], [], [], [], [], [], [], []];
	var secTotals = Array(8).fill(0);
	var percentiles = [];
	var secBounds;
	
	switch (AssmtType) {
	case "spm":
		invConds = [{
			type: "<",
			value: 10
		}, {
			type: "===",
			value: 58
		}];
		secBounds = [10, 21, 29, 40, 45, 55, 66];
		break;
	case "spmc":
		invConds = [{
			type: "<",
			value: 10
		}];
		secBounds = [10, 17, 24, 32, 36, 43, 52];
	}
	
	raResultsSPM = Object.values(raResultsSPM);
	raResultsSPM.forEach(function(a, b) {
		a = getScore(a, b);
		let secInd, i;
		// Programaticaly determine the index based on the sections defined for each type.
		for(i = 0;i < secBounds.length; i++){
			if(i == secBounds.length-1 || b < secBounds[i]){
				secInd = i;
				break;
			}
		}
		resultsBySection[secInd].push(a);
		secTotals[secInd] += a;
	});
	var temp = document.getElementById("rowtemp");
	var table = document.getElementById("results");
	secs.forEach(function(head, ind) {
		let total = ind < 4 ? secTotals[ind]: secTotals[ind + 1];
		// Not all possible scores will be defined in raPercentiles so if we get an anomalous score skip it here 
		// (the return statement leaves the current function iteration; the next interation will proceed -- it is like 'continue' in a normal for loop)
		if( !(total in raPercentilesSPM) ) return; 

		let row = temp.content.cloneNode(true).firstElementChild;
		let percentile = raPercentilesSPM[total][percentileKeys[ind]];
		percentiles.push(percentile);
		let classToAdd = false;
		switch (interp(percentile)) {
		case "Definite Dysfunction": 
			classToAdd = "dd";
			break;
		case "Some Problems":
			classToAdd = "sp";
			break;
		case "Typical":
			classToAdd = "tp";
		}
		
		classToAdd && row.classList.add(classToAdd);
		let values = [head, total, interp(percentile), percentile + "%", 100 - percentile + "%"];
		for (var iter = 0; iter < row.children.length; iter++) {
			row.children[iter].innerHTML = values[iter];
		}
		table.appendChild(row);
	});
	percentiles.push(percentiles.reduce((a, b) => Number(a) + Number(b)) / percentiles.length);
	var draw = new CustomEvent("draw", {detail: percentiles});
	document.getElementById("chart").dispatchEvent(draw);
});
function interp(percentile) {
	if (percentile > 97)
		return "Definite Dysfunction";
	if (percentile < 84)
		return "Typical";
	return "Some Problems";
}
function getScore(char, index) {
	var x = function() {
		switch(char) {
		case "a":
			return 4;
		case "f":
			return 3;
		case "o":
			return 2;
		case "n":
			return 1;
		}
	}();
	if (doInv(index)) {
		x = 5 - x;
	}
	return x;
}

function doInv(value) {
	for (var cond of invConds.values()) {
		var str = "return " + value + cond.type + cond.value +";";
		if (new Function(str)())
			return true;
	}
	return false;
}