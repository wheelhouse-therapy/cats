var tabData;
var activeTab, tabContent;
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
				loadSidebar(responseText.raOut);
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

function loadSidebar(data) {
	var header = document.getElementById("sidebar-header");
	header.innerHTML = data.header;
	tabData = data.tabs;
	var tabs = document.querySelectorAll(".tab");
	// loop through all the tabs, giving the onclicks if we have HTML for them
	// and hiding them otherwise
	for (var i = 0; i < tabs.length; i++) {
		if (tabData[tabs[i].id]) {
			tabs[i].addEventListener("click", changeTab);
		} else {
			tabs[i].style.display = "none";
		}
		// set the tab name based on the AJAX return also
		tabs[i].innerHTML = data.tabNames[i];
	}
	activeTab = document.querySelector(".active-tab");
	if (!activeTab) {
		// if for some reason there's no active tab
		// make the first tab active
		tabs[0].classList.add("active-tab");
		activeTab = tabs[0];
	}
	if (activeTab.style.display === "none") {
		// if the active tab can't be seen by the current user (i.e dev only or doesn't exist)
		// make the first tab active
		activeTab.classList.remove("active-tab");
		tabs[0].classList.add("active-tab");
		activeTab = tabs[0];
	}
	tabContent = document.getElementById("tab-content");
	tabContent.innerHTML = tabData[activeTab.id];
}
function changeTab(e) {
	var name = e.target.id;
	activeTab.classList.remove("active-tab");
	e.target.classList.add("active-tab");
	activeTab = e.target;
	tabContent.innerHTML = tabData[name];
}