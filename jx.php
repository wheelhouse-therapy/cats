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
$rJX['raOut'][] = $cmd;

/* The permission level of ajax commands is defined by the format of the command.
 *
 * foo-bar      : if Read  permission on "foo" perm, do command bar
 * foo--bar     : if Write permission on "foo" perm, do command bar
 * foo---bar    : if Admin permission on "foo" perm, do command bar
 *
 * Commands with no hyphens are available to everyone.
 */
list($bOk, $dummy, $rJX['sErr']) = $oApp->sess->IsAllowed( $cmd );

if(!$bOk){
    // Safety measure incase someone without perms try to run a command.
    // Since the command processing code below is not garenteed to check for permission.
    if(CATS_SYSADMIN){echo "Escaping to done. NO PERMISSION";}
    goto done;
}

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
    case 'clinicImg':
        $img_id = $_REQUEST['image_ID'];
        $action = $_REQUEST['action'];
        $clinic = new Clinics($oApp);
        switch ($img_id){
            case "slogo":
                $img_id = Clinics::LOGO_SQUARE;
                break;
            case "wlogo":
                $img_id = Clinics::LOGO_WIDE;
                break;
            case "footer":
                $img_id = Clinics::FOOTER;
                break;
        }
        $rJX['bOk'] = true;
        $rJX['sOut'] = $clinic->setImage($img_id,$action=="Restore");
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
            $oClinics = new Clinics($oApp);
            $clinic_key = @$oClinics->getClinicsByName($location)[0];
            if(!$clinic_key){
                $clinic_key = @$oClinics->getClinicsByCity($location)[0];
            }
            $email = 'cats@catherapyservices.ca';
            $oClinicsDB = new ClinicsDB($oApp->kfdb);
            $kfr = $oClinicsDB->GetClinic($clinic_key);
            if($client_key && $kfr && $kfr->Value('email')){
                $email = $kfr->Value('email');
            }
            $message = "Message from:".@$ra['name']."\n\n";
            $message .= @$ra['message'];
            $rJX['bOk'] = mail($email,"Message for CATS Therapy",$message,"From: ".@$ra['email']);
        }
        else{
            $rJX['sErr'] = "Please Select a location";
        }
        break;
    case 'loadMenu':
        if(!$oApp->sess->IsLogin()){
            $rJX['sErr'] = "<div class='alert alert-danger'><strong>An Error Occured:</strong>Session Expired</div>";
            goto done;
        }
        $menu = SEEDInput_Str('screen');
        preg_match('/(?\'perm\'\w+)(?\'level\'-*)\w*/', $menu,$matches);
        $perm = $matches['perm'];
        switch($matches['level']){
            case '---':
                $rJX['bOk'] = $oApp->sess->CanAdmin($perm);
                $rJX['sErr'] = "Requires admin permission";
                break;
            case '--':
                $rJX['bOk'] = $oApp->sess->CanWrite($perm);
                $rJX['sErr'] = "Requires write permission";
                break;
            case '-':
            default:
                $rJX['bOk'] = $oApp->sess->CanRead($perm);
                $rJX['sErr'] = "Requires read permission";
                break;
        }
        $rJX['sErr'] = "<div class='alert alert-danger'><strong>An Error Occured:</strong>{$rJX['sErr']}</div>";
        $oUI = new CATS_MainUI( $oApp );
        $rJX['sOut'] = $oUI->Screen();
        $oHistory = new ScreenManager($oApp);
        $oHistory->restoreScreen(-1);
        break;
    case 'support':
        $people = (new ManageUsers($oApp))->getClinicRecord($oApp->sess->getUID());
        if($people && $email = $people->Value("P_email")){
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
    case 'tutorialComplete':
        require_once CATSLIB.'tutorial.php';
        $rJX['bOk'] = true;
        $screen = SEEDInput_Str('screen');
        TutorialManager::setComplete($oApp, $screen);
        break;
}

if( substr( $cmd, 0, 9 ) == 'catsappt-' ) {
    require_once CATSLIB."calendar.php";

    $kAppt = SEEDInput_Int( 'kAppt' );

    $o = new Appointments( $oApp );
    $rJX = $o->Cmd( $cmd, $kAppt, $_REQUEST );  // although ajax normally uses POST, use REQUEST here so we can test jx.php with GET e.g. directly in a browser
}
else if(substr($cmd,0,7) == 'system-'){
    switch($cmd){
        case 'system-footergenerator':
            $gen = new ImageGenerator($oApp);
            $rJX['sOut'] = $gen->processCMDs(SEEDInput_Str('action'),SEEDInput_Int('clinic_id'));
            if($rJX['sOut']){
                $rJX['bOk'] = TRUE;
            }
            else{
                $rJX['sErr'] = "Unknown CMD";
            }
            break;
    }
}
else if(substr($cmd, 0,22) == 'therapist--clientlist-'){
    $clientList = new ClientList($oApp);
    $rJX['raOut'] = $clientList->proccessCommands(substr($cmd, 22));
    $rJX['bOk'] = true;
}
else if( substr($cmd, 0, 10) == 'therapist-'){
    switch($cmd){
        case 'therapist-akaunting-xlsx':
            require_once CATSLIB."AkauntingReports.php";
            AkauntingReport_OutputXLSX( $oApp );
            exit;
            break;
        case 'therapist-akaunting-updateReport':
            require_once CATSLIB."AkauntingReports.php";
            $rJX['sOut'] = AkauntingReport( $oApp, true );
            $rJX['sOut']=utf8_encode($rJX['sOut']);
            $rJX['bOk'] = $rJX['sOut']?true:false;
            break;
        case 'therapist-assessment-check':
            $p_sAsmtType = SEEDInput_Str('sAsmtType');
            $kClient = SEEDInput_Int('fk_clients2');
            $assessments = new AssessmentsCommon($oApp);
            $asmt = $assessments->GetNewAsmtObject( $p_sAsmtType );
            $rJX['bOk'] = $asmt->checkEligibility($kClient, SEEDInput_Str("date"));
            $rJX['sOut'] = $asmt->getIneligibleMessage();
            break;
        case "therapist-assessments-clientlist":
            $client_key = SEEDInput_Int("fk_clients2");
            if($client_key <= 0){
                $rJX['sErr'] = "Client Key Must be positive (>0)";
                $rJX['sOut'] = $cmd;
                goto done;
            }
            $rJX['sOut'] = "
                            <!-- the div that represents the modal dialog -->
                            <div class=\"modal fade\" id=\"asmt_dialog\" role=\"dialog\">
                                <div class=\"modal-dialog\" id='asmtDialog'>
                                    <div class=\"modal-content\">
                                        <div class=\"modal-header\">
                                            <h4 class=\"modal-title\">Assessment Results for [[client]]</h4>
                                        </div>
                                        <div class=\"modal-body\">
                                            <div id='asmtData'>
                                                [[asmts]]
                                            </div>
                                        </div>
                                        <div class=\"modal-footer\">
                                            <a href='?screen=therapist-assessments&client_key={$client_key}'><button>Add New Assessment</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>";
            $raC = $oApp->kfdb->QueryRA("SELECT P.first_name as first_name,P.last_name as last_name FROM people as P, clients2 as C WHERE C.fk_people=P._key AND C._key=$client_key");
            $rJX['sOut'] = str_replace("[[client]]", $raC['first_name']." ".$raC['last_name'], $rJX['sOut']);
            $raA = $oApp->kfdb->QueryRowsRA("SELECT _key,date,_created,testType FROM `assessments_scores` WHERE fk_clients2 = ".$client_key);
            $s = "";
            foreach($raA as $ra){
                $s .= "<div style='cursor: pointer;' onclick='window.location=\"?screen=therapist-assessments&kA={$ra['_key']}&client_key={$client_key}\"'>"
                .$ra['testType']
                .": "
                    .AssessmentsCommon::GetAssessmentDate($ra)
                    ."</div>";
            }
            $rJX['sOut'] = str_replace("[[asmts]]",$s?:"No Assessment Data Recorded",$rJX['sOut']);
            $rJX['bOk'] = $rJX['sOut']?true:false;
            break;
        case 'therapist-clientlist-view':
            $_SESSION['clientListView'] = $_REQUEST['view'] == 'true';
            $clientList = new ClientList($oApp);
            ClientsAccess::getAccess(true,@$_SESSION['clientListView']?ClientsAccess::LIMITED:ClientsAccess::QUERY);
            $rJX['sOut'] = $clientList->drawList(ClientList::CLIENT)[0];
            $rJX['bOk'] = true;
            break;
        case 'therapist-clientlistxls':
            require_once CATSLIB."therapist-clientlistxls.php";
            Therapist_ClientList_OutputXLSX( $oApp );
            exit;
            break;
        case 'therapist-clientList-sort':
            //Store the clientlist sort params in the session variable
            $oApp->sess->VarSet("clientlist-normal", SEEDInput_Str("clientlist-normal",''));
            $oApp->sess->VarSet("clientlist-discharged", SEEDInput_Str("clientlist-discharged",''));
            $rJX['bOk'] = true;
            break;
        case 'therapist-clientList-form':
            header("Cache-Control: no-cache");
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
            $rJX['sOut'] = drawModal($oForm->GetValuesRA(), $clientList->oPeopleDB, ClientList::$pro_roles_name );
            $rJX['bOk'] = true;
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
        case 'therapist-distribute-reports-update-client':
            require_once CATSLIB."DistributeReports.php";
            $rJX["sOut"] = drawForm($oApp,$oApp->sess->SmartGPC("idOut"));
            $rJX["bOk"] = true;
            break;
        case 'therapist-fcd-canEmail':
            $kClient = SEEDInput_Int('client');
            $rJX['bOk'] = (new PeopleDB($oApp))->GetKFR(ClientList::CLIENT, $kClient)->Value('P_email')?true:false;
            break;
        case 'therapist-generate-address-labels':
            require_once CATSLIB."DistributeReports.php";
            // 'info' should be an array but this allows it to be a string too (not sure why that happens sometimes)
            $info = $_REQUEST["info"];
            if( !is_array($info) )  $info = [$info];
            $o = new DistributeReports($oApp);
            $o->OutputAddressLabels($info);
            die();
            break;
        case 'therapist-generate-fax-cover':
            require_once CATSLIB."DistributeReports.php";
            if( ($info = SEEDInput_Str('info')) ) {
                $o = new DistributeReports($oApp);
                $o->OutputFaxCover($info);
            }
            break;
        case 'therapist-generate-cover-letters':
            require_once CATSLIB."DistributeReports.php";
            if( ($info = SEEDInput_Str('info')) ) {
                $o = new DistributeReports($oApp);
                $o->OutputCoverLetter($info);
            }
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
                            $rJX['sOut'] = "No Assessments";
                        }
                        break;
                }
            }
            break;
        case 'therapist-resource-search':
            if(!$oApp->sess->IsLogin()){
                $rJX['sErr'] = "<strong>An Error Occured:</strong>Session Expired";
                goto done;
            }
            $search = SEEDInput_Str("search");
            $ra = ResourceRecord::GetRecordFromGlobalSearch($oApp, $search);
            if($ra instanceof ResourceRecord){
                $dir = FilingCabinet::GetDirInfo($ra->getDirectory())['name'];
                if($ra->getSubDirectory()){
                    $dir .= '/'.$ra->getSubDirectory();
                }
                $href = FilingCabinet::GetAccessor($ra);
                $rJX['sOut'] = "<div><a href='{$href}'>{$ra->getFile()}</a> in {$dir}</div>";
            }
            else if($ra == NULL){
                $rJX['sOut'] = "No Results";
            }
            else{
                $rJX['sOut'] = "";
                foreach($ra as $oRR){
                    $dir = FilingCabinet::GetDirInfo($oRR->getDirectory())['name'];
                    if($oRR->getSubDirectory()){
                        $dir .= '/'.$oRR->getSubDirectory();
                    }
                    $href = FilingCabinet::GetAccessor($oRR);
                    $rJX['sOut'] .= "<div><a href='{$href}'>{$oRR->getFile()}</a> in {$dir}</div>";
                }
            }
            $rJX['bOk'] = true;
            break;
        case 'therapist-resource-upload':
            if(!$oApp->sess->IsLogin()){
                $rJX['sErr'] = "<strong>An Error Occured:</strong>Session Expired";
                goto done;
            }
            $rJX['sErr'] = "<strong>An Unexpected Error Occured:</strong>Upload was not successful. Please inform the developers of error immediately";
            $oFC = new FilingCabinetUpload( $oApp );
            $rJX['sOut'] = $oFC->UploadToPending();
            $rJX['bOk'] = ($rJX['sOut']?true:false);
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
        case 'admin-userform':
            require_once CATSLIB.'manage_users.php';
            $manageUsers = new ManageUsers($oApp);
            $rJX['sOut'] = $manageUsers->manageUser(SEEDInput_Int('staff_key'));
            $rJX['bOk'] = $rJX['sOut'] != '';
            break;
        case 'admin-userform-submit':
            require_once CATSLIB.'manage_users.php';
            $manageUsers = new ManageUsers($oApp);
            $rJX['raOut'] = $manageUsers->saveForm();
            $rJX['bOk'] = $rJX['raOut'] != [];
            break;
        case 'admin-usercommand':
            require_once CATSLIB.'manage_users.php';
            $manageUsers = new ManageUsers($oApp);
            $action = SEEDInput_Str('action');
            $uid = SEEDInput_Int('uid');
            $rJX['sOut'] = $manageUsers->processCommands($action,$uid);
            $rJX['bOk'] = $rJX['sOut'] != '';
            break;
        case 'admin-userclone':
            require_once CATSLIB.'manage_users.php';
            $manageUsers = new ManageUsers($oApp);
            $uid = SEEDInput_Int('uid');
            $rJX['sOut'] = $manageUsers->manageUser(0,true,$uid);
            $rJX['bOk'] = $rJX['sOut'] != '';
            break;
    }
}
else if( SEEDCore_StartsWith( $cmd, 'resourcestag-' ) ) {
    $tag = SEEDInput_Str('tag');
    $id = SEEDInput_Int('id');
    $oRR = ResourceRecord::GetRecordByID($oApp, $id);

    switch( $cmd ) {
        case 'resourcestag--newtag':
            $oRR->addTag($tag);
            $oRR->StoreRecord();
            $rJX['bOk'] = true;
            break;
        case 'resourcestag--deletetag':
            // tags are stored \tA\tB\tC\t
            $oRR->removeTag($tag);
            $oRR->StoreRecord();
            $rJX['bOk'] = true;
            break;
    }
}


done:

echo json_encode($rJX);

?>