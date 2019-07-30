function getForm(id) {
	var sidebar = document.getElementById("sidebar");
	sidebar.classList.remove("open");
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText.bOk == "true") {
				sidebar.innerHTML = this.responseText.sOut;
				sidebar.classList.add("open");
			}
		}
	};
	xhttp.open("POST", "jx.php", true);
	xhttp.send("id=" + id + "&cmd=therapist-clientList-form");
}