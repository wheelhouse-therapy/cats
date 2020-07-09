function submitForm(e){
	var formData = new FormData(document.getElementById('upload-file-form'));
	formData.append("cmd","therapist-resource-upload");
	e.currentTarget.parentElement.innerHTML = "<button><i class='fa fa-sync-alt fa-spin'></i> Uploading</button>";
    debugger;
	$.ajax({
        type: "POST",
        data: formData,
        cache       : false,
        contentType : false,
        processData : false,
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
        	debugger;
            var jsData = JSON.parse(data)
            if(jsData.bOk){
				document.getElementById('uploadForm').innerHTML = jsData.sOut;
            }
            else{
            	document.getElementById('uploadForm').innerHTML = "<div class='alert alert-danger'>"+jsData.sErr+"</div>"
            }
        },
        error: function(jqXHR, status, error) {
        	debugger;
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function resetForm(){
	document.getElementById('uploadForm').innerHTML = upload;
}