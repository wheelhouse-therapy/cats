var chars = ["n", "s", "o", "f"]
addEventListener("DOMContentLoaded", function() {
	var inputs = document.body.querySelectorAll("input.score-item");
	
	inputs.forEach(function(a, b){
		a.addEventListener("keypress", function() {
			if (checkInput(event, this)) {
				try {
					inputs[b + 1].focus();
				} catch(e) {}
			}
		});
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