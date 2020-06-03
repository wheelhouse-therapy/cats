function toggleCollapse(el, trans = true) {
	var contents = el.nextElementSibling;
	if (contents.offsetHeight != 0) {
		contents.dataset.height = contents.offsetHeight;
	}
	if (trans) {
		contents.style.height = contents.dataset.height + "px";
	}
	setTimeout(() => {
		el.parentElement.classList.toggle("collapsed");
		el.firstElementChild.classList.toggle("fa-folder-open");
		el.firstElementChild.classList.toggle("fa-folder");
	}, 0);
}

function clearHeight(e) {
	if (e.propertyName === "height") {
		e.target.style.height = "";
	}
}

addEventListener("DOMContentLoaded", () => {
	var list = document.querySelectorAll(".folder-title");
	for (var i = 0; i < list.length; i++) {
		toggleCollapse(list[i], false);
	}
});