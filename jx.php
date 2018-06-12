<?php

/* Entry point for CATS AJAX
 *
 * Input:
 *     cmd = the command to execute
 *
 * Output:
 *     bOk   = true/false; the command was successful
 *     sOut  = text, string, html output for a successful command
 *     raOut = json-ized array of output values for a successful command
 *     sErr  = error message if !bOk
 */

require_once "_start.php" ;

//header( "Content-type: application/json" );

$rJX = array( 'bOk' => false,
              'sOut' => "",
              'raOut' => array(),
              'sErr' => "",
);

$cmd = SEEDInput_Str('cmd');


/* The permission level of ajax commands is defined by the format of the command.
 *
 * foo-bar      : if Read  permission on "foo" perm, do command bar
 * foo--bar     : if Write permission on "foo" perm, do command bar
 * foo---bar    : if Admin permission on "foo" perm, do command bar
 *
 * Commands with no hyphens are available to everyone.
 */
list($bOk, $dummy, $rJX['sErr']) = $oApp->sess->IsAllowed( $cmd );


switch( $cmd ) {
    case 'appt_newform':
        require_once CATSLIB."calendar.php";
        if( ($clientId = @$_POST['cid']) ) {
            $o = new Calendar( $oApp );
            $o->createAppt($_POST);
            $rJX['sOut'] = (new ClientsDB($oApp->kfdb))->getClient($clientId)->Value("client_name");
            $rJX['bOk'] = true;
        } else {
            $rJX['sErr'] = "Unspecified client";
        }
        break;
}

if( substr( $cmd, 0, 9 ) == 'catsappt-' ) {
    require_once CATSLIB."calendar.php";

    $kAppt = SEEDInput_Int( 'kAppt' );

    $o = new Appointments( $oApp );
    $rJX = $o->Cmd( $cmd, $kAppt, $_POST );
}

done:

echo json_encode($rJX);

?>