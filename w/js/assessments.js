var inverted;
var secs;
addEventListener("DOMContentLoaded", function() {
	var inputs = document.body.querySelectorAll("input.score-item");
	var scores = document.body.querySelectorAll("span.score");
	var sectionTotals = document.querySelectorAll("span.sectionTotal");
	
	switch (document.querySelector("input[name='sAsmtType']").value) {
	case "spm":
		inverted = function(i) {
			return (i < 10) || (i == 56);
		}
		secs = [10, 21, 29, 40, 45, 55, 66];
		cols.splice(4, 0, "");
		break;
	case "spmc":
		inverted = function(i) {
			return (i < 10);
		}
		secs = [10, 17, 24, 32, 36, 43, 52];
		cols.splice(4, 0, "");
	}
	
	inputs.forEach(function(a, b){
		for (var val in secs) {
			if (b < secs[val]) {a.section = val; break;}
		}
		if (a.section === undefined) {a.section = 7;}
		if(typeof inverted !== 'undefined' && a.value !== ""/*document.querySelector("input[name='sAsmtType']").value !== "aasp"*/){
			scores[b].innerHTML = getScore(a.value, b);
		}
		a.addEventListener("keydown", function(e) {
			if (!e.key || noAbsorb(e.key)) {return;}
			if (checkInput(e, this)) {
				if(typeof inverted !== 'undefined'/*document.querySelector("input[name='sAsmtType']").value !== "aasp"*/){
					scores[b].innerHTML = getScore(e.key, b);
					updateTotal(scores, this.section, sectionTotals);
				}
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
			if(typeof inverted !== 'undefined'/*document.querySelector("input[name='sAsmtType']").value !== "aasp"*/){
				scores[b].innerHTML = getScore(e.data, b);
				updateTotal(scores, this.section, sectionTotals);
			}
			inputs[b + 1].focus();
		})
		a.addEventListener("paste", function(e) {e.preventDefault();});
	});
	updateAllTotals(scores, sectionTotals);
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
	if (inverted(index)) {
		x = 5 - x;
	}
	return x;
}
function updateTotal(scores, section, sectionTotals) {
	debugger;
	var secCount = 0;
	var count = 0;
	var last = true;
	scores.forEach(function(a) {
		let sec = a.previousElementSibling.section;
		let score = Number(a.innerHTML);
		if (sec !== "0" && sec !== "7")
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
		if (section == "4") {return;}
		var percentile = raPercentilesSPM[secCount][cols[section]];
		if (!percentile) {return;}
		if (sectionTotals[section]) {
			sectionTotals[section].innerHTML += " (" + percentile + "%).";
		}
	}
}
function updateAllTotals(scores, sectionTotals) {
	for (var i = 0; i < secs.length; i++) {
		updateTotal(scores, i.toString(), sectionTotals);
	}
}