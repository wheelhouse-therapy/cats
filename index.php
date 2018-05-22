<?php

require_once "_start.php";
require_once CATSLIB."therapist-clientlist.php";

/* If you get the error Cannot connect to database, you probably have to execute these two MySQL commands:
        CREATE DATABASE ot;
        GRANT ALL ON ot.* to 'ot'@'localhost' IDENTIFIED BY 'ot'" );

   Check that the tables exist and recreate them if necessary
*/
createTables($oApp->kfdb);


if (!file_exists('pending_resources')) {
    @mkdir('pending_resources', 0777, true);
    echo "Pending Resources Directiory Created<br />";
}
if (!file_exists('accepted_resources')) {
    @mkdir('accepted_resources', 0777, true);
    echo "Accepted Resources Directiory Created<br />";
}

if( !$oApp->sess->IsLogin() ) {
    echo "<form style='margin:auto;border:1px solid gray; width:33%; padding: 10px; border-radius:10px; background-color:#b3f0ff; margin-top:10em;' method='post'>"
         ."<h1 style='text-align:center; font-family: sans-serif'>Login to CATS</h1>"
         ."<input type='text' placeholder='Username' style='display:block; margin:auto; border-radius:5px; border-style: inset outset outset inset; background-color:#99ff99;' name='seedsession_uid' />"
         ."<br />"
         ."<input type='password' placeholder='Password' style='display:block; margin:auto; border-radius:5px; border-style: inset outset outset inset; background-color:#99ff99;' name='seedsession_pwd' />"
         ."<br />"
         ."<input type='submit' value='Login' style='border-style: inset outset outset inset; background-color:#99ff99; border-radius:5px; display:block; margin:auto;' />"
         ."</form>";

    // This is where we store the user's current screen. If they have logged out, or the login expired, reset their screen to the default.
    $oApp->sess->VarSet( 'screen', "" );

    exit;
}

/*
if( $oApp->sess->CanRead('admin') ) echo "<p>I can read Administration things</p>";
if( $oApp->sess->CanWrite('admin') ) echo "<p>I can write Administration things</p>";
if( $oApp->sess->CanRead('leader') ) echo "<p>I can read Leader things</p>";
if( $oApp->sess->CanWrite('leader') ) echo "<p>I can write Leader things</p>";
if( $oApp->sess->CanRead('therapist') ) echo "<p>I can read Therapist things</p>";
if( $oApp->sess->CanWrite('therapist') ) echo "<p>I can write Therapist things</p>";
if( $oApp->sess->CanRead('client') ) echo "<p>I can read Client things</p>";
if( $oApp->sess->CanWrite('client') ) echo "<p>I can write Client things</p>";
*/



/* UI and Command Paradigm:

   The session variable 'screen' controls which screen you see next. To move to a different screen, issue http parm "screen=foo".

   Commands are processed before screens are drawn. Issue "cmd=bar" to make something happen.

   screen names and cmd names have three parts:  perm level name

          perm is a SEEDSessionPerms perm (we use therapist, leader, admin, etc)
          level is one, two, or three hyphens:
              -    = read access required
              --   = write access required
              ---  = admin access required
          name is the name of the screen or command

          e.g. the screen called therapist-calendar requires read access on the "therapist" perm
               the screen called therapist--editclients requires write access on the "therapist" perm
               the screen called admin-showpermissions requires read access on the "admin" perm

               the command called client-appt requires read access on the "client" perm
               the command called leader--addusers requires write access on the "leader" perm
               the command called calendar---addcalendar requires admin access on the "calendar" perm
*/

$oUI = new CATS_MainUI( $oApp );

//var_dump($_REQUEST);
//var_dump($_SESSION);

$s = "";

$screen = $oApp->sess->SmartGPC( 'screen' );
$oUI->SetScreen($screen == ""?"home":$screen);
if( substr($screen,0,5) == 'admin' ) {
    $s .= drawAdmin($oApp);
} else if( substr( $screen, 0, 9 ) == "therapist" ) {
    $s .= drawTherapist( $screen, $oApp );
} else if($screen == "logout"){
    $s .= drawLogout($oApp);
} else {
    $s .= drawHome($oApp);
}
echo $oUI->OutputPage( $s );

?>

