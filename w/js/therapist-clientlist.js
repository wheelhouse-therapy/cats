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
            debugger;
        }
    });
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
	$(":input",e.currentTarget).prop("disabled", false);
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
        location.reload();
    });
}