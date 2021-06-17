<?php
require_once '_start.php';

const CLINICS = ["Brant","Guelph"];

const MESSAGE = <<<Message
Hi everyone,
Here is your friendly reminder that payroll spreadsheets are due by 3pm on the 21st.
Thanks!
Sue + Alison
Message;

if(php_sapi_name() !== 'cli' && strrpos(php_sapi_name(), "cgi") === false){
    die("Can only be run by cli");
}

$clinics = new Clinics($oApp);
$manageUsers = new ManageUsers2($oApp);

$indentLevel = 0;
function outputString($msg){
    global $indentLevel;
    echo "<pre>";
    for($i=0;$i<$indentLevel;$i++){
        echo "\t";
    }
    echo $msg;
    echo "</pre>";
}

function handleClinic($key){
    global $clinics;
    global $manageUsers;
    $users = $clinics->getUsersInClinic($key);
    foreach($users as $user){
        $raData = $manageUsers->getClinicProfile($user['_key'])['kfr']->ValuesRA();
        if($raData['fk_people']){
            if(@$raData['P_email']){
                SEEDEmailSend(["payroll@catherapyservices.ca","CATS Payroll"], $raData['P_email'], "Payroll Reminder", MESSAGE,"",['reply-to'=>"developer@catherapyservices.ca"]);
                outputString("Email sent to {$user['realname']}");
            }
            else{
                outputString("User {$user['realname']} does not have a email");
            }
        }
        else{
            outputString("User {$user['realname']} doesn't have a linked person profile");
        }
    }
}

foreach(CLINICS as $name){
    outputString("Handleing clinic: {$name}");
    $indentLevel++;
    $clinicKeys = $clinics->getClinicsByName($name);
    if(count($clinicKeys) > 1){
        outputString("ALERT: Multiple clinics with the name");
        $indentLevel++;
        foreach($clinicKeys as $key){
            outputString("Handeling clinic {$key}");
            $indentLevel++;
            handleClinic($key);
            $indentLevel--;
        }
        $indentLevel--;
    }
    else if(count($clinicKeys) == 1){
        handleClinic($clinicKeys[0]);
    }
    else{
        outputString("No Clinic found");
    }
    $indentLevel--;
}