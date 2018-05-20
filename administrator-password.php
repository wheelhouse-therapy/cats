<?php
if($_POST['password'] != "seeds"){
    http_response_code(401);
    die();
}