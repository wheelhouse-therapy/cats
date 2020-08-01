<?php

var_dump($_FILES);

$s = "

<form method='post' id='upload-file-form' enctype='multipart/form-data'>
<input type='hidden' name='cmd' value='therapist-resource-upload' />
Select file to upload (up to ".ini_get('upload_max_filesize')."b):
<input type='file' name='fileToUpload' id='fileToUpload' required><br />
<input type='submit' id='upload-file-button' value='Upload File' name='submit'/>
</form>
";

echo $s;
