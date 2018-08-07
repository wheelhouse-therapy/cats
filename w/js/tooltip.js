var elements = document.querySelectorAll("[data-tooltip]");
var template = document.createElement("span");
template.classList.add("tooltiptext");
for (var x of elements.values()) {
	x.classList.add("tooltip");
	template.innerHTML = x.dataset.tooltip;
	x.insertBefore(template.cloneNode(true), x.firstElementChild);
	x.tooltip = x.firstElementChild;
	x.tooltip.style.visibility = 'hidden';
	x.onmouseenter = function() {
		var store = this;
		this.fade = setTimeout(function() {store.tooltip.style.opacity = 1;}, 600);
		this.tooltip.style.visibility = 'visible';
	};
	x.onmouseleave = function() {
		clearTimeout(this.fade);
		this.tooltip.style.opacity = 0;
		this.tooltip.addEventListener("transitionend", unShow);
	};
}
function unShow() {
	if (getComputedStyle(this).opacity !== 0) return;
	this.style.visibility = 'hidden';
	this.removeEventListener("transitionend", unShow);
}