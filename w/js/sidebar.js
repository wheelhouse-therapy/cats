function getForm(id) {
	var selected = document.querySelector(".selected");
	if (selected) {
		selected.classList.remove("selected");
	}
	var clicked = document.querySelector("[data-id=" + id + "]");
	if (clicked) {
		clicked.classList.add("selected");
	}
	var sidebar = document.getElementById("sidebar");
	sidebar.classList.remove("open");
	sidebar.setAttribute("data-open-id",id);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var responseText = JSON.parse(this.responseText);
			if (responseText.bOk == true) {
				sidebar.innerHTML = responseText.sOut;
				sidebar.classList.add("open");
			}
			else{
				sidebar.removeAttribute("data-open-id");
			}
		}
	};
	xhttp.open("GET", "jx.php?cmd=therapist-clientList-form&id=" + id, true);
	xhttp.send();
}

function reloadForm(){
	var sidebar = document.getElementById("sidebar");
	if(sidebar.classList.contains("open")){
		getForm(sidebar.getAttribute("data-open-id"));
	}
}
function closeSidebar() {
	document.getElementById("sidebar").classList.remove("open");
	if(document.querySelector(".selected")){
		document.querySelector(".selected").classList.remove("selected");
	}
}