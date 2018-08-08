var elements = document.querySelectorAll("[data-tooltip]");
var template = document.createElement("span");
template.classList.add("catstooltiptext");
for (var x of elements.values()) {
	x.classList.add("catstooltip");
	template.innerHTML = x.dataset.tooltip;
	x.insertBefore(template.cloneNode(true), x.firstElementChild);
	x.catstooltip = x.firstElementChild;
	x.catstooltip.style.visibility = 'hidden';
	x.onmouseenter = function() {
		var store = this;
		this.fade = setTimeout(function() {store.catstooltip.style.opacity = 1;}, 600);
		this.catstooltip.style.visibility = 'visible';
	};
	x.onmouseleave = function() {
		clearTimeout(this.fade);
		this.catstooltip.style.opacity = 0;
		this.catstooltip.addEventListener("transitionend", unShow);
	};
}
function unShow() {
	if (getComputedStyle(this).opacity !== 0) return;
	this.style.visibility = 'hidden';
	this.removeEventListener("transitionend", unShow);
}