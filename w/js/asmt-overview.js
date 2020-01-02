var invConds;
var noCount;
addEventListener("DOMContentLoaded", function() {
	var secs = ["Social Participation", "Vision", "Hearing", "Touch", "Taste/Smell",
		"Body Awareness", "Balance and Motion", "Planning", "Total"];
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
			value: 56
		}];
		secBounds = [10, 21, 29, 40, 45, 55, 66];
		noCount = [0, 6];
		break;
	case "spmc":
		invConds = [{
			type: "<",
			value: 10
		}];
		secBounds = [10, 17, 24, 32, 36, 43, 52];
		noCount = [0, 6];
	}
	
	raResultsSPM = Object.values(raResultsSPM);
	raResultsSPM.forEach(function(a, b) {
		a = getScore(a, b);
		let secInd, i;
		// Programaticaly determine the index based on the sections defined for each type.
		for(i = 0;i < secBounds.length; i++){
			if(b < secBounds[i]){
				secInd = i;
				break;
			}
		}
		if (secInd === undefined) secInd = secBounds.length;
		resultsBySection[secInd].push(a);
		secTotals[secInd] += a;
	});
	var temp = document.getElementById("rowtemp");
	var table = document.getElementById("results");
	secs.forEach(function(head, ind) {
		var final = false;
		if (ind == 8) {final = true;}
		var total;
		if (final) total = addUp(secTotals);
		else total = secTotals[ind];

		let row = temp.content.cloneNode(true).firstElementChild;
		var percentile;
		if (final) percentile = raTotalsSPM[total];
		else if (ind == 4) percentile = "N/A";
		else percentile = raPercentilesSPM[total][percentileKeys[(ind < 4? ind: ind - 1)]];
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
		let values = ind == 4? [head, total, "N/A", "N/A", "N/A"]:
				[head, total, interp(percentile), percentile + "%", 100 - percentile + "%"];
		for (var iter = 0; iter < row.children.length; iter++) {
			row.children[iter].innerHTML = values[iter];
		}
		table.appendChild(row);
	});
	percentiles.splice(4, 1);
	var draw = new CustomEvent("draw", {detail: percentiles});
	document.getElementById("chart").dispatchEvent(draw);
});
function interp(percentile) {
	if (percentile == "N/A") return;
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

function addUp(secTots) {
	var tots = [];
	for (var i in secTots) {
		if (noCount.includes(Number(i))) {
			tots[i] = 0;
		}
		else {
			tots[i] = Number(secTots[i]);
		}
	}
	return tots.reduce((a, b) => a + b);
}