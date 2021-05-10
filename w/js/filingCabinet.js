function toggleCollapse(el, transition = true) {
	if (el.parentElement.classList.contains("empty")) {
		return; //empty folder, can't expand
	}
	var contents = el.nextElementSibling;
	if (contents.offsetHeight != 0) {
		contents.dataset.height = contents.offsetHeight;
	}
	if (transition) {
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
	var reqData, cb;
	var name = target.children[1];
	while (name.tagName !== "A") {
		name = name.nextElementSibling;
	}
	name = name.lastElementChild;
	switch (button.dataset.action) {
	case "rename":
		let newName = prompt("Enter a new name for the file:");
		if(!newName){
			return;
		}
		let invalidRegex = /[\\/:*?"<>|]/;
		if (invalidRegex.test(newName)) {
			alert("The characters \\ / : * ? \" < > | are not allowed in filenames");
			return;
		}
		let extensionRegex = /(\.docx|\.doc|\.pdf|\.rtf|\.txt)$/;
		if (!extensionRegex.test(newName)) {
			let fileParts = name.innerText.split(".");
			let oldExtension = "." + fileParts[fileParts.length - 1];
			newName += oldExtension;
		}
		reqData = {
			cmd: "admin-rename-resource",
			to: newName
		};
		cb = function(data) {
			if (!data.bOk) {
				alert("Failed to rename the resource");
			}
			else {
				name.innerHTML = data.sOut;
			}
		};
		break;
	case "delete":
		if (confirm("Are you sure you want to delete " + name.innerText + "?")) {
			reqData = {
				cmd: "admin-delete-resource"
			};
			cb = function(data) {
				if (!data.bOk) {
					alert("Failed to delete the resource");
				}
				else {
					if (target.nextElementSibling === null && target.previousElementSibling === null) {
						// only element in the folder, collapse it and set it as empty before removing
						var folder = target.parentElement.parentElement;
						var folderTitle = folder.firstElementChild;
						while (!folderTitle.classList.contains("folder-title")) {
							folderTitle = folderTitle.nextElementSibling;
						}
						toggleCollapse(folderTitle);
						folderTitle.innerHTML += " (empty)";
						folder.classList.add("empty");
					}
					target.remove();
				}
			}
		} else {
			return;
		}
		break;
	case "reorder-left":
		if (target.previousElementSibling === null) {
			return;
		}
		reqData = {
			cmd: "admin-reorder-resource-left"
		};
		cb = function(data) {
			if (!data.bOk) {
				alert("Failed to reorder the resource");
			} else {
				var swap = target.previousElementSibling;
				[target.outerHTML, swap.outerHTML] = [swap.outerHTML, target.outerHTML];
			}
		};
		break;
	case "reorder-right":
		if (target.nextElementSibling === null) {
			return;
		}
		reqData = {
			cmd: "admin-reorder-resource-right"
		};
		cb = function(data) {
			if (!data.bOk) {
				alert("Failed to reorder the resource");
			} else {
				var swap = target.nextElementSibling;
				[target.outerHTML, swap.outerHTML] = [swap.outerHTML, target.outerHTML];
			}
		};
		break;
	}
	reqData.id = target.id;
	$.ajax({
		url: 'jx.php',
		data: reqData,
		dataType: "json",
		success: cb
	});
	console.log(target.id, button.dataset.action);
}

addEventListener("DOMContentLoaded", () => {
	var list = document.querySelectorAll(".folder-title");
	for (var i = 0; i < list.length; i++) {
		toggleCollapse(list[i], false);
	}
	hideEmptyFolders();
});