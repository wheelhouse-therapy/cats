var cooldown;

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
function clinicHack(e) {
	$("select",e.currentTarget.form).prop("disabled", false);
}

function getForm(staffKey){
	clearTimeout(cooldown);
	$.ajax({
        type: "POST",
        data: {cmd:'admin-userform',staff_key:staffKey},
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("form").innerHTML = jsData.sOut;
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function submitForm(e){
	var formData = new FormData(e.target.form);
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
            	document.getElementById('users').innerHTML = jsData.raOut.list;
            	document.getElementById("form").innerHTML = jsData.raOut.form;
            }
            else{
            	document.getElementById("form").innerHTML = "";
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function executeCMD(command,userID){
	$.ajax({
        type: "POST",
        data: {cmd:'admin-usercommand',uid:userID,action:command},
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("form").innerHTML = jsData.sOut;
            	cooldown = setTimeout(function(){
            		let staff_key = document.getElementById('staff_key').value;
            		getForm(staff_key);
            	}, 5000);
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
}

function cloneRecord(e,userID){
    $.ajax({
        type: "POST",
        data: {cmd:'admin-userclone',uid:userID},
        url: "jx.php",
        success: function(data, textStatus, jqXHR) {
        	var jsData = JSON.parse(data);
            if(jsData.bOk){
            	document.getElementById("form").innerHTML = jsData.sOut;
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function addSync(element){
	element.innerHTML = "<i class='fa fa-sync-alt fa-spin'></i>"+element.innerHTML;
}