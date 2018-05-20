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

$oUI = new CATS_UI();

//var_dump($_REQUEST);
//var_dump($_SESSION);

$s = "";

$screen = $oApp->sess->SmartGPC( 'screen' ); // SEEDInput_Str( 'screen' );
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

function  drawLogout($oApp){
    $oApp->sess->LogoutSession();
    return("<head><meta http-equiv=\"refresh\" content=\"0; URL=".CATSDIR."\"></head><body>You have Been Logged out<br /><a href=".CATSDIR."\"\">Back to Login</a></body>");
}

function drawHome($oApp)
{
    global $oUI;

    $s = $oUI->Header()."<h2>Home</h2>";
    $s .= ($oApp->sess->CanRead('therapist')?"<a href='?screen=therapist' class='toCircle catsCircle1'>Therapist</a>":"").($oApp->sess->CanRead('admin')?"<a href='?screen=admin' class='toCircle' data-format='200px red blue'>Admin</a>":"");
    $s .= (!$oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist-calendar' class='toCircle catsCircle1'>Calendar</a>":"");
    return( $s );
}
function drawTherapist( $screen, $oApp )
{
    global $oUI;
    $s = $oUI->Header()."<h2>Therapist</h2>";
    switch( $screen ) {
        case "therapist":
        default:
            $s .= "<p>What would you like to do?</p>"
                ."<div class='container-fluid'>"
                ."<div class='row'>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=home' class='toCircle catsCircle1'>Home</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-materials' class='toCircle catsCircle2'>Print Handouts</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-formscharts' class='toCircle catsCircle1'>Print Forms for Charts</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-linedpapers' class='toCircle catsCircle2'>Print Different Lined Papers</a>"
                ."</div>"
                ."</div>"
                ."<div class='row'>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-entercharts' class='toCircle catsCircle2'>Enter Clients</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-ideas' class='toCircle catsCircle2'>Get Ideas</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-downloadcustommaterials' class='toCircle catsCircle1'>Download Marketable Materials</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-team' class='toCircle catsCircle1'>Meet the Team</a>"
                ."</div>"
                ."</div>"
                ."<div class='row'>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-submitresources' class='toCircle catsCircle2'>Submit Resources to Share</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-clientlist' class='toCircle catsCircle1'>Clients and Providers</a>"
                ."</div>"
                ."<div class='col-md-3'>"
                ."<a href='?screen=therapist-calendar' class='toCircle catsCircle1'>Calendar</a>"
                ."</div>"
                ."</div>"
                ."</div>";
                break;
        case "therapist-materials":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "PRINT HANDOUTS";
            break;
        case "therapist-formscharts":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "PRINT FORMS FOR CHARTS";
            break;
        case "therapist-linedpapers":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "PRINT DIFFERENT LINED PAPERS";
            break;
        case "therapist-entercharts":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "ENTER CHARTS";
            break;
        case "therapist-ideas":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "GET IDEAS";
            break;
        case "therapist-downloadcustommaterials":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= DownloadMaterials( $oApp );
            break;
        case "therapist-team":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "MEET THE TEAM";
            break;
        case "therapist-submitresources":
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= "SUBMIT RESOURCES";
            $s .= "<form action=\"share_resorces_upload.php\" method=\"post\" enctype=\"multipart/form-data\">
                Select resource to upload:
                <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
                <br /><input type=\"submit\" value=\"Upload File\" name=\"submit\">
                </form>";
            break;
        case "therapist-clientlist":
            $o = new ClientList( $oApp->kfdb );
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= $o->DrawClientList();
            break;
        case "therapist-calendar":
            require_once CATSLIB."calendar.php";
            $o = new Calendar( $oApp );
            $s .= ($oApp->sess->CanAdmin('therapist')?"<a href='?screen=therapist' >Therapist</a><br />":"");
            $s .= $o->DrawCalendar();
    }
    return( $s );
}
function drawAdmin($oApp)
{
    global $oUI;
    $s = "";
    if(SEEDInput_Str("screen") == "admin-droptable"){
        $oApp->kfdb->Execute("drop table ot.clients");
        $oApp->kfdb->Execute("drop table ot.clients_pros");
        $oApp->kfdb->Execute("drop table ot.professionals");
        $oApp->kfdb->Execute("drop table ot.SEEDSession_Users");
        $oApp->kfdb->Execute("drop table ot.SEEDSession_Groups");
        $oApp->kfdb->Execute("drop table ot.SEEDSession_UsersXGroups");
        $oApp->kfdb->Execute("drop table ot.SEEDSession_Perms");
        $s .= "<div class='alert alert-success'> Oops I miss placed your data</div>";
    }
    $s .= $oUI->Header()."<h2>Admin</h2>";
    $s .= "<a href='?screen=home' class='toCircle catsCircle2'>Home</a><a href='?screen=therapist' class='toCircle catsCircle2'>Therapist</a>";
    if($oApp->sess->CanAdmin("DropTables")){
        $s .= "<button onclick='drop();' class='toCircle catsCircle2'>Drop Tables</button>"
        ."<script>function drop(){
          var password = prompt('Enter the admin password');
          $.ajax({
                url: 'administrator-password.php',
                type: 'POST',
                data: {'password':password},
                cache: 'false',
                success: function(result){
                    location.href = '?screen=admin-droptable';
                },
                error: function(jqXHR, status, error){
                    alert('You are not authorized to perform this action');
                }
          });
          }</script>";
    }
    if($oApp->sess->CanWrite("admin")){$s .= "<a href='review_resources.php' class='toCircle catsCircle2'>Review Resources</a>";}
        return( $s );
}

?>
