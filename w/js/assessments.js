cols.splice(4, 0, "");
var invConds;
var secs;
addEventListener("DOMContentLoaded", function() {
	var inputs = document.body.querySelectorAll("input.score-item");
	var scores = document.body.querySelectorAll("span.score");
	var sectionTotals = document.querySelectorAll("span.sectionTotal");
	
	switch (document.querySelector("input[name='sAsmtType']").value) {
	case "spm":
		invConds = [{
			type: "<",
			value: 10
		}, {
			type: "===",
			value: 56
		}];
		secs = [10, 21, 29, 40, 45, 55, 66];
		break;
	case "spmc":
		invConds = [{
			type: "<",
			value: 10
		}];
		secs = [10, 17, 24, 32, 36, 43, 52];
	}
	
	inputs.forEach(function(a, b){
		for (var val in secs) {
			if (b < secs[val]) {a.section = val; break;}
		}
		if (a.section === undefined) {a.section = 7;}
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
	if (doInv(index)) {
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
		let score = Number(a.innerHTML);
		if (sec !== 0 && sec !== 7)
			count += score;
		if (sec === section) {
			secCount += score;
			if (!a.innerHTML) {last = false;}
		}
	});
	if( sectionTotals[section] ) {
		sectionTotals[section].innerHTML = "Section total: " + secCount;
	}
	document.getElementById("total").innerHTML = "Total score: " + count;
	if (last) {
		if (section == 4) {return;}
		debugger;
		var percentile = raPercentilesSPM[secCount][cols[section]];
		if (!percentile) {return;}
		if( sectionTotals[section] ) {
			sectionTotals[section].innerHTML += " (" + percentile + "%).";
		}
	}
}
function doInv(value) {
	for (var cond of invConds.values()) {
		var str = "return " + value + cond.type + cond.value +";";
		if (new Function(str)())
			return true;
	}
	return false;
}