var tabData;
var activeTab, tabContent;
var sidebarDiv;
var sidebarHeader;
var selected;
var tabs;
function getForm(id) {
	var clicked = document.querySelector("[data-id=" + id + "]");
	if (clicked === selected) {
		return;
	}
	if (selected) {
		selected.classList.remove("selected");
		selected = null;
	}
	if (clicked) {
		clicked.classList.add("selected");
		selected = clicked;
	}
	sidebarDiv.classList.remove("open");
	sidebarDiv.setAttribute("data-open-id",id);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var responseText = JSON.parse(this.responseText);
			if (responseText.bOk == true) {
				loadSidebar(responseText.raOut);
				sidebarDiv.classList.add("open");
			}
			else{
				sidebarDiv.removeAttribute("data-open-id");
			}
		}
	};
	xhttp.open("GET", "jx.php?cmd=therapist-clientList-form&id=" + id, true);
	xhttp.send();
}

function reloadForm(){
	if(sidebarDiv.classList.contains("open")){
		getForm(sidebarDiv.getAttribute("data-open-id"));
	}
}
function closeSidebar() {
	sidebarDiv.classList.remove("open");
	if (selected){
		selected.classList.remove("selected");
		selected = undefined;
	}
}

function loadSidebar(data) {
	sidebarHeader.innerHTML = data.header;
	tabData = data.tabs;
	// loop through all the tabs, giving the onclicks if we have HTML for them
	// and hiding them otherwise
	for (var i = 0; i < tabs.length; i++) {
		if (tabData[tabs[i].id]) {
			tabs[i].addEventListener("click", changeTab);
			tabs[i].style.display = "";
		} else {
			tabs[i].removeEventListener("click", changeTab);
			tabs[i].style.display = "none";
		}
		// set the tab name based on the AJAX return also
		tabs[i].innerHTML = data.tabNames[i];
	}
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
	tabContent.innerHTML = tabData[activeTab.id];
}
function changeTab(e) {
	var target = e.currentTarget;
	activeTab.classList.remove("active-tab");
	target.classList.add("active-tab");
	activeTab = target;
	tabContent.innerHTML = tabData[target.id];
}

window.addEventListener("DOMContentLoaded", function() {
	sidebarDiv = document.getElementById("sidebar");
	sidebarHeader = document.getElementById("sidebar-header");
	tabs = document.querySelectorAll(".tab");
	tabContent = document.getElementById("tab-content");
});