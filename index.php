<?php

require_once "_start.php";

/* If you get the error Cannot connect to database, you probably have to execute these two MySQL commands:
        CREATE DATABASE ot;
        GRANT ALL ON ot.* to 'ot'@'localhost' IDENTIFIED BY 'ot'" );

   Check that the tables exist and recreate them if necessary
*/
createTables($oApp->kfdb);


if (!file_exists(CATSDIR_RESOURCES)) {
    @mkdir(CATSDIR_RESOURCES, 0777, true);
    echo "Resources Directiory Created<br />";
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

$screen = $oApp->sess->SmartGPC( 'screen', array( 'home' ) );   // if array has only one element it is the default if screen==''
$s = $oUI->Screen( $screen );

echo $oUI->OutputPage( $s );

?>

