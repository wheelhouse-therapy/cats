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
function clinicHack(e) {
	$(".sfAp_clinic",e.currentTarget).prop("disabled", false);
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
	var postData = $(e.currentTarget).serializeArray();
	postData = postData.map(function(value, index, array){
        if(value.name == 'cmd'){
            return {name: 'cmd', value: 'therapist--clientlist-'+value.value};
        }
        return value;
    });
    $.ajax({
        type: "POST",
        data: postData,
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
        	var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("messageBox").innerHTML = jsData.raOut.message;
            	$("#messageBox").slideDown(100);
            	document.getElementById(jsData.raOut.listId).innerHTML = jsData.raOut.list;
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

function clientDischargeToggle() {
	var client = document.querySelector("[data-id=" + document.getElementById('sidebar').dataset.id + "]");
	client.classList.toggle('client-discharged');
	client.classList.toggle('client-normal');
	document.getElementById('client-form').submit();
}