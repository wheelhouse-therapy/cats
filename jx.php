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
$rJX['sOut'] = $cmd;

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
        $array = array_filter($_REQUEST, function($key) {
            return strpos($key, 'et_pb_contact_') === 0;
        },ARRAY_FILTER_USE_KEY );
        $ra = array();
        foreach ($array as $key=>$value){
            $ra[explode("_", $key)[3]] = $value;
        }
        if($location = @$ra['location']){
            $message = "Message from:".@$ra['name']."\n\n";
            $message .= @$ra['message'];
            $rJX['bOk'] = mail($location."@catherapyservices.ca","Message for CATS Therapy",$message,"From: ".@$ra['email']);
        }
        else{
            $rJX['sErr'] = "Please Select a location";
        }
        break;

    case 'support':
        $people = (new PeopleDB($oApp))->getKFRCond("P","uid='{$oApp->sess->GetUID()}'");
        if($people && $email = $people->Value("email")){
            $rJX['bOk'] = mail("developer@catherapyservices.ca",SEEDInput_Str('supportType')." from ".$people->Expand("[[first_name]] [[last_name]]"),SEEDInput_Str('supportDesc'),"From: ".$email);
        }
        else {
            $rJX['sErr'] = "You must have an associated person record with an email address set in order to use the support request feature";
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
        case 'therapist-akaunting-xlsx':
            require_once CATSLIB."AkauntingReports.php";
            AkauntingReport_OutputXLSX( $oApp );
            exit;
            break;

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
            $message = "Here are the credentials to sign in to %s's account.\r\nUsername: %s\r\nPassword: %s\r\n Thanks for using CATS";
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
            require_once CATSLIB.'modal-submit.php';
            break;
        case 'therapist-resourcemodal':
            unset($_REQUEST['cmd']);
            if(SEEDInput_Str("submitVal") == "Next"){
                switch($oApp->sess->SmartGPC('screen')){
                    case "therapist-reports":
                        require_once CATSLIB.'assessments.php';
                        $assessments = new AssessmentsCommon($oApp);
                        if($assmts = $assessments->listAssessments(SEEDInput_Int('client'))){
                            $rJX['sOut'] = json_encode($assmts);
                            $rJX['bOk'] = true;
                        }
                        else{
                            $rJX['sOut'] = "No Assements";
                        }
                        break;
                }
            }
            break;
        case 'therapist-clientList-sort':
            //Store the clientlist sort params in the session variable
            $oApp->sess->VarSet("clientlist-normal", SEEDInput_Str("clientlist-normal",''));
            $oApp->sess->VarSet("clientlist-discharged", SEEDInput_Str("clientlist-discharged",''));
            $rJX['bOk'] = true;
            break;
        case 'therapist-assessment-check':
            $p_sAsmtType = SEEDInput_Str('sAsmtType');
            $kClient = SEEDInput_Int('fk_clients2');
            $assessments = new AssessmentsCommon($oApp);
            $asmt = $assessments->GetNewAsmtObject( $p_sAsmtType );
            $rJX['bOk'] = !$asmt->checkEligibility($kClient, SEEDInput_Str("date"));
            $rJX['sOut'] = $asmt->getIneligibleMessage();
            break;
        case 'therapist-clientList-form':
            require_once CATSLIB.'therapist-clientlist.php';
            $key = SEEDInput_Str("id");
            if(!$key){
                $rJX['bOk'] = false;
                $rJX['sErr'] = "Missing Key";
                goto done;
            }
            list($type,$pid) = ClientList::parseID($key);
            $clientList = new ClientList($oApp);
            $rJX['sOut'] = $clientList->DrawAjaxForm($pid,$type);
            $rJX['bOk'] = ($rJX['sOut']?true:false);
            header("Cache-Control: no-cache");
            break;
        case 'therapist--clientModal':
            require_once CATSLIB."client-modal.php" ;
            $client_key = SEEDInput_Int("client_key");
            if($client_key <= 0){
                $rJX['bOk'] = false;
                $rJX['sErr'] = "Client Key must be positive";
                goto done;
            }
            $clientList = new ClientList($oApp);
            $kfr = $clientList->oPeopleDB->GetKFR(ClientList::CLIENT, $client_key );
            $oForm = new KeyframeForm( $clientList->oPeopleDB->KFRel(ClientList::CLIENT), "A", array("fields"=>array("parents_separate"=>array("control"=>"checkbox"))));
            $oForm->SetKFR($kfr);
            $rJX['sOut'] = drawModal($oForm->GetValuesRA(), $clientList->oPeopleDB, $clientList->pro_roles_name );
            $rJX['bOk'] = true;
            break;
    }
}
else if(substr($cmd, 0, 6) == 'admin-'){
    switch($cmd){
        case 'admin-ResourceTrees':
            // This Handles changes in resource trees that are open
            // under manage resources.
            // This is important since the server should reopen open resource trees automaticly on page reload
            if(SEEDInput_Get("open")['plain']){
                $oApp->sess->SmartGPC('open');
            }
            else{
                // The User has closed all the trees.
                // Unset the session variable so that we dont reopen trees which the user has closed
                // On the next reload
                $oApp->sess->VarUnSet("open");
            }
            break;
    }
}
else if( substr($cmd,0,strlen('resourcestag-')) == 'resourcestag-' ) {
    if( $cmd == 'resourcestag--newtag' ) {
        $dbFolder = addslashes(SEEDInput_Str('folder'));
        $dbFilename = addslashes(SEEDInput_Str('filename'));
        $dbTag = addslashes(SEEDInput_Str('tag'));

        $cond = "folder='$dbFolder' AND filename='$dbFilename'";
        if( $oApp->kfdb->Query1( "SELECT tags FROM resources_files WHERE $cond" ) ) {
            $oApp->kfdb->Execute( "UPDATE resources_files SET tags=CONCAT(tags,'$dbTag','\t') WHERE $cond" );
        } else {
            $oApp->kfdb->Execute( "INSERT INTO resources_files (folder,filename,tags) VALUES ('$dbFolder','$dbFilename','\t$dbTag\t')" );
        }
    }
}


done:

echo json_encode($rJX);

?>