<?php
require_once '_start.php';

if( @$_SERVER['REQUEST_METHOD'] == 'POST' ) {
    // A form was submitted. Defer processing until the page is reloaded via 303, which causes the browser to do a GET on the given location.
    $_SESSION['passwrdReset'] = $_POST;
    header( "Location: {$_SERVER['PHP_SELF']}?reset=reset", true, 303 );
    exit;
}

echo "<head><link rel='icon' href='https://catherapyservices.ca/wp-content/uploads/2018/08/cropped-cats_icon-32x32.jpg' sizes='32x32'><script src='".W_CORE_URL."js/SEEDCore.js'></script></head>";
echo "<div style='position:absolute; top:5px; left:5px;'><a href='./'><img src='".CATS_LOGO."' style='max-width:300px;float:left;'/></a></div>";

$oUserDB = new SEEDSessionAccountDB($oApp->kfdb, 1);
if(SEEDInput_Str("reset") && isset($_SESSION['passwrdReset']) && $oUserDB->GetUserInfo($_SESSION['passwrdReset']['uid'])[0]){
    echo "<div style='text-align:center;margin:auto;border:1px solid gray; width:33%; padding: 10px; padding-top: 0px; border-radius:10px;; margin-top:10em;'>";
    echo "<h1 style=\"text-align:center; font-family: 'Lato', sans-serif; font-weight: 300; font-size: 30pt\">Reset Password</h1>";
    $raUser = $oUserDB->GetUserInfo($_SESSION['passwrdReset']['uid'])[1];
    $username = $_SESSION['passwrdReset']['uid'];
    $oPeopleDB = new PeopleDB( $oApp );
    $ra = $oPeopleDB->KFDB()->QueryRA("SELECT A.* FROM `people` as A, seedsession_users as B WHERE A.uid=B._key AND B.email='".$username."'");
    if($ra['email']){
        $body = "Hi [[realname]],\n"
               ."A Password reset request was recieved for your account:[[email]].\n"
               ."Your password has been reset to 'cats'. You will be prompted to change it the next time you log in.\n"
               ."\nThanks for using the CATS system.\n"
               ."CATS Dev Team";
       if(mail($ra['email'], "Password Reset Request", SEEDCore_ArrayExpand($raUser, $body))){
           $oUserDB->ChangeUserPassword($raUser[0], "cats");
           echo "Password Reset Email Sent.";   
       }
       else{
           echo "Could not send Pasword Reset Email<br />Contact <a href='mailto:developer@catherapyservices.ca'>developer@catherapyservices.ca</a> for assistance";
       }
    }
    else{
        echo "Your account does not have an email connected<br />Contact <a href='mailto:developer@catherapyservice.ca'>developer@catherapyservices.ca</a> to have your password reset";
    }
    echo "<br /><a href='./'>Back to Login</a>";
    unset($_SESSION['passwrdReset']);
}
else{
    echo "<form style='margin:auto;border:1px solid gray; width:33%; padding: 10px; padding-top: 0px; border-radius:10px;; margin-top:10em;' method='post'>"
             ."<h1 style=\"text-align:center; font-family: 'Lato', sans-serif; font-weight: 300; font-size: 30pt\">Reset Password</h1>"
             ."<div style='text-align:center;'>Enter your username to reset your password.<br />If you don't know your username contact <a href='mailto:developer@catherapyservices.ca'>developer@catherapyservices.ca</a></div>"
             ."<br />"
             ."<input type='text' placeholder='Username' style='display:block; font-family: \"Lato\", sans-serif; font-weight: 400; margin:auto; border-radius:5px; border-style: inset outset outset inset;' name='uid' />"
             ."<br />"
             ."<input type='submit' value='Reset' style='border-style: inset outset outset inset; font-family: \"Lato\", sans-serif; font-weight: 400; border-radius:5px; display:block; margin:auto;' />"
             ."</form>";
}
echo "<script> SEEDCore_CleanBrowserAddress(); </script>";