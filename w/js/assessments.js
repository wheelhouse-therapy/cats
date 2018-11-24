cols.splice(4, 0, "");
addEventListener("DOMContentLoaded", function() {
	var inputs = document.body.querySelectorAll("input.score-item");
	var scores = document.body.querySelectorAll("span.score");
	var sectionTotals = document.querySelectorAll("span.sectionTotal")
	
	inputs.forEach(function(a, b){
		if (b < 10) {a.section = 0;}
		else if (b < 21) {a.section = 1;}
		else if (b < 29) {a.section = 2;}
		else if (b < 40) {a.section = 3;}
		else if (b < 45) {a.section = 4;}
		else if (b < 55) {a.section = 5;}
		else if (b < 66) {a.section = 6;}
		else {a.section = 7;}
		a.addEventListener("keydown", function(e) {
			if (!e.key || noAbsorb(e.key)) {return;}
			if (checkInput(e, this)) {
				scores[b].innerHTML = getScore(e.key, b);
				updateTotal(scores, this.section, sectionTotals);
				try {
					inputs[b + 1].focus();
				} catch(e) {}
			}
		});
		a.addEventListener("input", function(e) {
			if (e.inputType && e.inputType.indexOf("delete") !== -1) {
				scores[b].innerHTML = "";
				updateTotal(scores, this.section, sectionTotals);
				return;
			}
			if (!e.data) {
				e.data = this.value;
			}
			scores[b].innerHTML = getScore(e.data, b);
			updateTotal(scores, this.section, sectionTotals);
			inputs[b + 1].focus();
		})
		a.addEventListener("paste", function(e) {e.preventDefault();});
	});
});
	

function checkInput(e, input) {
	e.preventDefault();
	if (chars.includes(e.key)) {
		input.value = e.key.toLowerCase();
		return true;
	}
	return false;
}
function noAbsorb(key) {
	if (key.search(/^[a-z0-9\\!"#$%&'()*+,.\/:;<=>?@\[\] ^_`{|}~-]$/i) !== -1) {
    	return false;
    }
	return true;
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
function updateTotal(scores, section, sectionTotals) {
	var secCount = 0;
	var count = 0;
	var last = true;
	scores.forEach(function(a) {
		let sec = a.previousElementSibling.section;
		if (sec !== 0 && sec !== 7)
			count += Number(a.innerHTML);
		if (sec === section) {
			secCount += Number(a.innerHTML);
			if (!a.innerHTML) {last = false;}
		}
	});
	
	sectionTotals[section].innerHTML = "Section total: " + secCount;
	document.getElementById("total").innerHTML = "Total score: " + count;
	if (last) {
		if (section === 4) {return;}
		var percentile = raPercentilesSPM[secCount][cols[section]];
		if (!percentile) {return;}
		sectionTotals[section].innerHTML += " (" + percentile + "%).";
	}
}