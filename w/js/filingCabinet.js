function toggleCollapse(el, trans = true) {
	if (el.parentElement.classList.contains("empty")) {
		return; //empty folder, can't expand
	}
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

function hideEmptyFolders() {
	folders = document.querySelectorAll(".folder-contents");
	for (var i = 0; i < folders.length; i++) {
		if (folders[i].childElementCount === 0) {
			folders[i].previousElementSibling.lastElementChild.innerHTML += " (empty)";
			folders[i].parentElement.classList.add("empty"); // this class does a greyout and prevents expanding the folder
		}
	}
}

addEventListener("DOMContentLoaded", () => {
	var list = document.querySelectorAll(".folder-title");
	for (var i = 0; i < list.length; i++) {
		toggleCollapse(list[i], false);
	}
	hideEmptyFolders();
});