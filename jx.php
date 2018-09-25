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

    case 'contact':
        $rJX['sOut'] = "Thank You";
        if($location = @$_POST['et_pb_contact_location_1']){
            $message = "Message from:".@$_POST['et_pb_contact_name_1']."\n\n";
            $message .= @$_POST['et_pb_contact_message_1'];
            $rJX['bOk'] = mail($location."@catherapyservices.ca","Message for CATS Therapy",$message,"From: ".@$_POST['et_pb_contact_email_1']);
        }
        else{
            $rJX['sErr'] = "Please Select a location";
        }
        break;
        
    case 'test':
        $test = SEEDInput_Str('test');
        if( $test == 'good' ) {
            $rJX['bOk'] = true;
            $rJX['sOut'] = "<h3>Hello world!</h3>";
        } else {
            $rJX['bOk'] = false;
            $rJX['sOut'] = "<h3>I'm so sorry!</h3>";
            $rJX['sErr'] = "That was bad";
        }
        goto done;
}

if( substr( $cmd, 0, 9 ) == 'catsappt-' ) {
    require_once CATSLIB."calendar.php";

    $kAppt = SEEDInput_Int( 'kAppt' );

    $o = new Appointments( $oApp );
    $rJX = $o->Cmd( $cmd, $kAppt, $_REQUEST );  // although ajax normally uses POST, use REQUEST here so we can test jx.php with GET e.g. directly in a browser
}
else if( substr($cmd, 0, 10) == 'therapist-'){
    switch($cmd){
        case 'therapist---credentials':
            $clientId = $_POST['client'];
            $peopleDB = new PeopleDB($oApp);
            $email = $peopleDB->getKFR("C", $clientId)->Value("email");
            $username = substr($clientDB->getClient($clientId)->Value("P_first_name"), 0,1);
            $username .= $clientDB->getClient($clientId)->Value("P_last_name");
            $username = strtolower($username);
            $dob = $clientDB->getClient($clientId)->Value("dob");
            $radob = explode("-", $dob,3);
            $dob = ""; // Reset dob for rearangement of dob
            for($c = count($radob)-1;$c >= 0;$c--){
                $dob .= $radob[$c];
            }
            $name = $clientDB->getClient($clientId)->Value("client_first_name");
            $name .= " ";
            $name .= $clientDB->getClient($clientId)->Value("client_last_name");
            $rJX['sOut'] = "Credentials sent to: " .$email;
            $accountDB = new SEEDSessionAccountDB($oApp->kfdb,$oApp->sess->GetUID());
            $message = "Here is the credentials to sign in to %s's account.\r\nUsername: %s\r\nPassword: %s\r\n Thanks for using CATS";
            if(($account = $accountDB->GetKUserFromEmail($username)) != 0){
                list($k,$user,$meta) = $accountDB->GetUserInfo($account);
                $dob = $user['password'];
                goto send;
            }
            $account = $accountDB->CreateUser( $username, $dob, array("realname" => $name, "gid1" => 5,"eStatus" => "ACTIVE") );
            $accountDB->SetUserMetadata( $account, "clientId", $clientId );
            $kfr = (new Users_ClinicsDB($oApp->kfdb))->KFRelBase()->CreateRecord();
            $kfr->SetValue("fk_users", $account);
            $kfr->SetValue("fk_clinics", (new Clinics($oApp))->GetCurrentClinic());
            $kfr->PutDBRow();
            send:
            $message = sprintf($message,$name,$username,$dob);
            $rJX['bOk'] = mail($email, "CATS Credentials for ".$name."'s Account", $message,"From: developer@catherapyservices.ca");
            break;

        case 'therapist-clientlistxls':
            require_once CATSLIB."therapist-clientlistxls.php";
            Therapist_ClientList_OutputXLSX( $oApp );
            exit;
            break;
        case 'therapist--modal':
            $rJX['bOk'] = true;
            require_once CATSLIB.'mobal-submit.php';
            break;
    }
}
done:

echo json_encode($rJX);

?>