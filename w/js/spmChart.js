var dAttr;
var chart;
var box;
var line;
var group;
var points;
var infoBox;
var infoText;
var fadeStore;
var hovering;
var boxExit, lineExit;
var tmx = ["46.682496", "74.512158", "102.34182", "130.17149", "158.00114", "185.8308", "213.66046", "241.49012"];
function percToPx(perc) {
	let hperc = perc * -0.025 + 2.5;
	return box.y.animVal.value + hperc * box.height.animVal.value;
}
function drawGraph(e) {
	var percTotals = e.detail;
	chart = document.getElementById("chart");
	box = document.getElementById("box");
	line = document.getElementById("line");
	group = document.getElementById("spmChart");
	points = document.querySelectorAll("circle.point");
	infoBox = document.getElementById("info");
	infoText = document.getElementById("info-text");
	chart.suspendRedraw(5000);
	if (!percScores) return;
	var scores = percScores.map(a => percToPx(a));
	dAttr = "M" + tmx[0] +" " + scores[0];
	drawPoint(tmx[0], scores[0], 0);
	for (var i = 1; i < scores.length; i++) {
		dAttr += " L" + tmx[i] + " " + scores[i];
		drawPoint(tmx[i], scores[i], i);
	}
	line.setAttribute("d", dAttr);
	line.classList.remove("hidden");
	chart.classList.remove("hidden");
	chart.unsuspendRedrawAll();
}
function grow(ele) {
	ele.style.strokeWidth = "1.25px";
}
function shrink(ele) {
	ele.style.strokeWidth = "";
	for (var i = 0; i < points.length; i++) {
		points[i].style.strokeWidth = "0.7";
	}
}
function scoreGrow() {
	line.style.strokeWidth = "1.75px";
	for (var i = 0; i < points.length; i++) {
		points[i].style.strokeWidth = "1.5";
	}
}
function drawPoint(x, y, i) {
	points[i].setAttribute("cx", x);
	points[i].setAttribute("cy", y);
	if (y < 91.020195 || y > 244.083335) points[i].style.display = "none";
}
function tip(e, text) {
	lineExit = false;
	boxExit = false;
	e.target.addEventListener("mouseout", clearTip);
	infoBox.addEventListener("mouseout", clearTip);
	fadeStore = setTimeout(function() {showTip(e, text)}, 600);
}
function showTip(e, text) {
	infoBox.style.visibility = "visible";
	hovering = e.target;
	infoText.innerHTML = text;
	infoBox.style.top = e.clientY + 5 + "px";
	infoBox.style.left = e.clientX + "px";
	infoText.style.opacity = 1;
}
function clearTip(e) {
	clearTimeout(fadeStore);
	if (e.target == hovering) lineExit = true;
	if (e.target == infoBox || e.target == infoText) boxExit = true;
	if (!(boxExit && lineExit)) return;
	visCheck(e.target);
	infoText.style.opacity = 0;
	infoBox.removeEventListener("mouseout", clearTip);
	hovering.removeEventListener("mouseout", clearTip);
}
function visCheck(ele) {
	visManage();
	ele.addEventListener("transitionend", visManage);
}
function visManage() {
	if (getComputedStyle(infoText).opacity !== "0") return;
	infoBox.style.visibility = "hidden";
	this.removeEventListener("transitionend", visCheck);
}
document.addEventListener("DOMContentLoaded", function() {
	document.getElementById("chart").addEventListener("draw", drawGraph);
})