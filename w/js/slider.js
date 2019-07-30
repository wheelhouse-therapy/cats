function getForm(id) {
	var sidebar = document.getElementById("sidebar");
	sidebar.classList.remove("open");
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var responseText = JSON.parse(this.responseText);
			if (responseText.bOk == true) {
				sidebar.innerHTML = responseText.sOut;
				sidebar.classList.add("open");
			}
		}
	};
	xhttp.open("GET", "jx.php?cmd=therapist-clientList-form&id=" + id, true);
	xhttp.send();
}