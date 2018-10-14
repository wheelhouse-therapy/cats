var x = document.createElement('img');
x.src = 'https://cdn1.iconfinder.com/data/icons/pixel-perfect-at-16px-volume-2/16/5001-128.png';
x.className = 'drop-arrow';
var z = document.getElementsByClassName('day');
for(y = 0; y < z.length; y++) {
	var w = x.cloneNode();
	var e = z[y].firstChild;
	z[y].insertBefore(w, e);
	w.onclick = rotateMe;
	e.onclick = rotateMe;
} 
function rotateMe(e) {
	e.preventDefault();
	this.parentElement.classList.toggle('collapsed');
}
function expand() {
	var days = document.getElementsByClassName('day');
	for (var loop = 0; loop < days.length; loop++) {
		days[loop].classList.remove('collapsed');
	}
}
function collapse() {
	var days = document.getElementsByClassName('day');
	for (var loop = 0; loop < days.length; loop++) {
		days[loop].classList.add('collapsed');
	}
}
function appt() {
    var x = this;
    while (!x.classList.contains('appointment')) {
        x = x.parentElement;
    }
return x;
}