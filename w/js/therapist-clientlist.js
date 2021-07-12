var searchBar;

var normalCheckbox;
var dischargedCheckbox;
var normalShown = true;
var dischargedShown = true;

var sidebar;
var firstNameInput;
var lastNameInput;

function connectButton(e,key) {
    $.ajax({
        type: "POST",
        data: {cmd:'therapist--clientModal',client_key:key},
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
                document.getElementById('modalBox').innerHTML = jsData.sOut;
                $('#contact_dialog').modal('show');
                modalLoaded();
            }
            else{
                console.log(jsData.sErr);
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function connectStaffButton(e,key) {
    $.ajax({
        type: "POST",
        data: {cmd:'therapist--staffModal',client_key:key},
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
                document.getElementById('modalBox').innerHTML = jsData.sOut;
                $('#staff_dialog').modal('show');
                staffModalLoaded();
            }
            else{
                console.log(jsData.sErr);
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function sendcreds(e){
    e.preventDefault();
    var credsDiv = document.getElementById('credsDiv');
    var cid = document.getElementById('clientId').value;
    $.ajax({
        type: 'POST',
        data: { cmd: 'therapist---credentials', client: cid },
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            var sSpecial = jsData.bOk ? jsData.sOut : 'Failed to send Email';
            credsDiv.innerHTML =  sSpecial;
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function doUpdateForm() {
    var sel = document.getElementById('mySelect').value;
    if( sel == 'Other' ) {
        document.getElementById('other').style.display = 'inline';
        document.getElementById('other').disabled = false;
    } else {
        document.getElementById('other').style.display = 'none';
        document.getElementById('other').disabled = true;
    }
}

function inSchool() {
    var checkBox = document.getElementById('schoolBox');
    var text = document.getElementById('schoolField');
    var hidden = document.getElementById('schoolHidden');
    if (checkBox.checked == true){
	     text.style.display = 'block';
	     text.disabled = false;
       hidden.disabled = true;
    } else {
	     text.style.display = 'none';
	     text.disabled = true;
       hidden.disabled = false;
    }
 }
 
 function parentsSeparate(){
 	var checkBox = document.getElementById('separateBox');
    var row = document.getElementById('additionalAddress');
    if (checkBox.checked == true){
	     row.style.display = '';
    } else {
	     row.style.display = 'none';
    }
 }
 
function clinicHack(e) {
	$("select",e.currentTarget.form).prop("disabled", false);
}

function updateAccountStyle(){
    var select = document.getElementById('newAccount');
    if(select.selectedOptions[0].value == 0){
        select.className = 'noAccount';
    }
    else{
        select.className = '';
    }
}

function modalLoaded() {
    $("#contact_form").on("submit", function(e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax({
            type: "POST",
            data: postData,
            url: formURL,
            success: function(data, textStatus, jqXHR) {
                $('#contact_dialog').modal('hide');
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
            }
        });
        e.preventDefault();
    });
    
    $("#submitForm").on('click', function() {
        $("#contact_form").submit();
    });
    $("#contact_dialog").on("hidden.bs.modal", function(){
        reloadForm();
    });
    setTimeout(function() {$(".searchable").select2({placeholder:'Select Provider',allowClear:true});}, 1);
}

function staffModalLoaded() {
    $("#staff_form").on("submit", function(e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax({
            type: "POST",
            data: postData,
            url: formURL,
            success: function(data, textStatus, jqXHR) {
                $('#staff_dialog').modal('hide');
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
            }
        });
        e.preventDefault();
    });
    
    $("#submitStaffForm").on('click', function() {
        $("#staff_form").submit();
    });
    $("#staff_dialog").on("hidden.bs.modal", function(){
        reloadForm();
    });
    setTimeout(function() {$(".searchable").select2({placeholder:'Select Provider',allowClear:true});}, 1);
}

function sendCMD(e, command){
	$("#messageBox").slideUp(100);
    $.ajax({
        type: "POST",
        data: {cmd:'therapist--clientlist-'+command},
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("messageBox").innerHTML = jsData.raOut.message;
            	$("#messageBox").slideDown(100);
            	if(jsData.raOut.id){
            		getForm(jsData.raOut.id);
            	}
            	hideAlerts();
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function submitForm(e){
	$("#messageBox").slideUp(100);
	var formData = new FormData(e.currentTarget.form);
	formData.append("action",e.currentTarget.value);
	formData.set("cmd","therapist--clientlist-"+formData.get('cmd'));
    $.ajax({
        type: "POST",
        data: formData,
        url: "jx.php",
        cache       : false,
        contentType : false,
        processData : false,
        success: function(data, textStatus, jqXHR) {
        	var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("messageBox").innerHTML = jsData.raOut.message;
            	$("#messageBox").slideDown(100);
            	document.getElementById(jsData.raOut.listId).innerHTML = jsData.raOut.list;
            	if(jsData.raOut.id){
            		getForm(jsData.raOut.id);
            	}
            	else{
            		closeSidebar();
            	}
            	hideAlerts();
            } 
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function submitSidebarForm(e){
	$("#messageBox").slideUp(100);
	var formData = new FormData(e.currentTarget);
	formData.append("action",e.submitter.value);
	formData.set("cmd","therapist--clientlist-"+formData.get('cmd'));
    $.ajax({
        type: "POST",
        data: formData,
        url: "jx.php",
        cache       : false,
        contentType : false,
        processData : false,
        success: function(data, textStatus, jqXHR) {
        	if(jqXHR.status === 205){
        		var location = jqXHR.getResponseHeader('Location');
        		window.location.replace(location);
        		return;
        	}
        	var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("messageBox").innerHTML = jsData.raOut.message;
            	$("#messageBox").slideDown(100);
            	document.getElementById(jsData.raOut.listId).innerHTML = jsData.raOut.list;
            	if(jsData.raOut.id){
            		getForm(jsData.raOut.id);
            	}
            	else{
            		closeSidebar();
            	}
            	hideAlerts();
            } 
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function clientDischargeToggle() {
	var client = document.querySelector("[data-id=" + sidebar.dataset.openId + "]");
	client.classList.toggle('client-discharged');
	client.classList.toggle('client-normal');
}

function search() {
	var query = new RegExp(searchBar.value, "i");
	var names = document.getElementsByClassName("name");
	for (var i = 0; i < names.length; i++) {
		if (!query.test(names[i].innerHTML)) {
			names[i].parentElement.classList.add("search-hidden");
		}
		else {
			names[i].parentElement.classList.remove("search-hidden");
		}
	}
}

function loadAsmtList(key){
	$.ajax({
        type: "POST",
        data: {cmd:'therapist-assessments-clientlist',fk_clients2:key},
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
                document.getElementById('modalBox').innerHTML = jsData.sOut;
                $('#asmt_dialog').modal('show');
            }
            else{
                console.log(jsData.sErr);
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function filterClients(e){
    var filterForm = document.getElementById('filterForm');
    var postData = $(filterForm).serializeArray();
    var formURL = $(filterForm).attr("action");
    $.ajax({
        type: "POST",
        data: postData,
        url: formURL,
        success: function(data, textStatus, jqXHR) {
            doFilterUpdate();
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e && e.preventDefault();
}

function doFilterUpdate(){
    var normalClients = document.getElementsByClassName('client-normal');
    var dischargedClients = document.getElementsByClassName('client-discharged');
    if (normalCheckbox.checked != normalShown) {
    	for (var i = 0; i < normalClients.length; i++) {
    		normalClients[i].classList.toggle("filter-hidden");
    		normalShown = !normalShown;
    	}
    }
    if (dischargedCheckbox.checked != dischargedShown) {
    	for (var i = 0; i < dischargedClients.length; i++) {
    		dischargedClients[i].classList.toggle("filter-hidden");
    		dischargedShown = !dischargedShown;
    	}
    }
}

function checkNameExists() {
	var firstName = document.getElementById("sfAp_P_first_name").value;
	var lastName = document.getElementById("sfAp_P_last_name").value;
	if (firstName == "" || lastName == "") return;
	var code = sidebar.dataset.openId;
	var list;
	if (code == "C0") {
		list = document.getElementsByClassName("client");
	}
	else if (code == "PI0") {
		list = document.getElementsByClassName("therapist");
	}
	else if (code == "PE0") {
		list = document.getElementsByClassName("pro");
	}
	else {
		return;
	}
	var matching = false;
	var pattern = new RegExp(firstName + " " + lastName, "i");
	var names = document.getElementsByClassName("name");
	for (var i = 0; i < names.length; i++) {
		if (pattern.test(names[i].innerHTML)) {
			matching = true;
		}
	}
	if (matching) {
		document.getElementById("name-exists").classList.add("shown");
	} else {
		document.getElementById("name-exists").classList.remove("shown");
	}
}

/**
 * @returns boolean - True if browser suppors 'date' input type.
 */
function browserSupportsDateInput() {
    var i = document.createElement("input");
    i.setAttribute("type", "date");
    return i.type !== "text";
}

function toggleView(e){
	$.ajax({
        type: "POST",
        data: {cmd:'therapist-clientlist-view',view:e.currentTarget.checked},
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
        	var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById('clients').innerHTML = jsData.sOut;
            } 
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function updateAge(e) {
	if (e.currentTarget.validity.valid) {
		var parts = e.currentTarget.value.split("-");
		var year = parts[0], month = parts[1], day = parts[2];
		var now = new Date();
		var curYear = now.getFullYear(), curMonth = now.getMonth() + 1, curDay = now.getDate();
		
		var ageMonth = curMonth - month, ageYear = curYear - year, ageDay = curDay - day;
		if (ageDay < 0) {
			ageMonth -= 1;
		}
		if (ageMonth < 0) {
			ageYear -= 1;
			ageMonth += 12;
		}
		document.getElementById("age").innerHTML = ageYear + " Years, " + ageMonth + " Months";
	}
	else {
		document.getElementById("age").innerHTML = "Invalid date format";
	}
}

function initPage() {
	searchBar = document.getElementById("searchbar");
	searchBar.addEventListener("input", search);
	searchBar.placeholder = "Search...";
	
	normalCheckbox = document.getElementById("normal-checkbox");
	dischargedCheckbox = document.getElementById("discharged-checkbox");
	filterClients(null);
	
	sidebar = document.getElementById("sidebar");
	
	if (browserSupportsDateInput()) {
		document.documentElement.className += ' supports-date';
	}
}

window.addEventListener("DOMContentLoaded", initPage);