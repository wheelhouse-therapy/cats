addEventListener("DOMContentLoaded", function() {
	var secs = ["Social Participation", "Vision", "Hearing", "Touch",
		"Body Awareness", "Balance and Motion", "Planning"];
	var percentileKeys = Object.keys(raPercentilesSPM[8]);
	var resultsBySection = [[], [], [], [], [], [], [], []];
	var secTotals = Array(8).fill(0);
	var percentiles = [];
	raResultsSPM = Object.values(raResultsSPM);
	raResultsSPM.forEach(function(a, b) {
		a = getScore(a);
		var secInd;
		if (b < 10) {secInd = 0;}
		else if (b < 21) {secInd = 1;}
		else if (b < 29) {secInd = 2;}
		else if (b < 40) {secInd = 3;}
		else if (b < 45) {secInd = 4;}
		else if (b < 55) {secInd = 5;}
		else if (b < 66) {secInd = 6;}
		else {secInd = 7;}
		
		resultsBySection[secInd].push(a);
		secTotals[secInd] += a;
	});
	var temp = document.getElementById("rowtemp");
	var table = document.getElementById("results");
	secs.forEach(function(head, ind) {
		let total = secTotals[ind];
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
	if (index < 10 || index === 58) {
		x = 5 - x;
	}
	return x;
}