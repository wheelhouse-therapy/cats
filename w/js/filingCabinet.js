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

function contextMenuHandler(target, button) {
	switch (button.dataset.action) {
	case "rename":
		let newName = prompt("Enter a new name for the file:");
		let invalidRegex = /[\\/:*?"<>|]/;
		if (invalidRegex.test(newName)) {
			alert("The characters \\ / : * ? \" < > | are not allowed in filenames");
			return;
		}
		let extensionRegex = /(\.docx|\.doc|\.pdf|\.rtf|\.txt)$/;
		if (!extensionRegex.test(newName)) {
			let fileParts = target.children[2].lastElementChild.innerText.split(".");
			let oldExtension = "." + fileParts[fileParts.length - 1];
			newName += oldExtension;
		}
		$.ajax({
			url: 'jx.php',
			data: {
				cmd: "admin-rename-resource",
				id: target.id,
				to: newName
			},
			dataType: "json",
			success: function(data) {
				if (!data.bOk) {
					alert("Failed to rename the resource");
				}
				else {
					target.children[2].lastElementChild.innerHTML = data.sOut;
				}
			}
		});
	}
	console.log(target.id, button.dataset.action);
}

addEventListener("DOMContentLoaded", () => {
	var list = document.querySelectorAll(".folder-title");
	for (var i = 0; i < list.length; i++) {
		toggleCollapse(list[i], false);
	}
	hideEmptyFolders();
});