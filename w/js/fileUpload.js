var currentUpload;
function submitForm(e){
	var formData = new FormData(document.getElementById('upload-file-form'));
	formData.append("cmd","therapist-resource-upload");
	e.currentTarget.parentElement.innerHTML = "<button id='uploading-button'><i class='fa fa-sync-alt fa-spin'></i> <span id='uploading-text'>Uploading</span></button>";
	currentUpload = $.ajax({
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			
			document.getElementById("upload-bar").classList.add("uploading");
			document.getElementById("filled-bar").style.width = "0";
			var uploadingButton = document.getElementById("uploading-button");
			uploadingButton.addEventListener("mouseover", function() {
				this.lastElementChild.innerHTML = "Cancel";
			});
			uploadingButton.addEventListener("mouseout", function() {
				this.lastElementChild.innerHTML = "Uploading";
			});
			uploadingButton.addEventListener("click", cancelUpload);
			xhr.upload.addEventListener("progress", function(evt) {
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					percentComplete = parseInt(percentComplete * 100);
					document.getElementById("progress-percentage").innerHTML = percentComplete + "%";
					document.getElementById("filled-bar").style.width = percentComplete + "%";
					
					if (percentComplete === 100) {
						document.getElementById("progress-percentage").innerHTML = "Finishing up..";
					}
					
				}
			}, false);
			
			return xhr;
		},
        type: "POST",
        data: formData,
        cache       : false,
        contentType : false,
        processData : false,
        url: 'jx.php',
        success: function(data, textStatus, jqXHR) {
            var jsData = JSON.parse(data);
            if(jsData.bOk){
				document.getElementById('uploadForm').innerHTML = jsData.sOut;
				//document.getElementById("progress-percentage").innerHTML = "Complete!";
            }
            else{
            	document.getElementById('uploadForm').innerHTML = "<div class='alert alert-danger'>"+jsData.sErr+"</div>"
            	document.getElementById("progress-percentage").innerHTML = "Error";
            }
        },
        error: function(jqXHR, status, error) {
            console.log(status + ": " + error);
        }
    });
    e.preventDefault();
}

function resetForm(){
	document.getElementById('uploadForm').innerHTML = upload;
}

function cancelUpload(e) {
	e.preventDefault();
	currentUpload.abort();
	resetForm();
}