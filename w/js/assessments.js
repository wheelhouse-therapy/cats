var chars = ["a", "s", "o", "n"]
addEventListener("DOMContentLoaded", function() {
	var inputs = document.body.querySelectorAll("input.score-item");
	
	inputs.forEach(function(a, b){
		a.addEventListener("keydown", function() {
			if (checkInput(event, this)) {
				try {
					inputs[b + 1].focus();
				} catch(e) {}
			}
		});
		a.addEventListener("input", function() {
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