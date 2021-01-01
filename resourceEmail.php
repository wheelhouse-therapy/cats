<?php
require_once '_start.php';

const template = <<<ResourceTemplate
[[TITLE]] --- Uploaded By: [[UPLOADER]]\n
ResourceTemplate;

function output(String $msg){
    echo "{$msg}<br />";
}

$recordBody = "";

$counter = 0;

foreach (ResourceRecord::GetResourcesByNewness($oApp) as $oRR){
    $recordBody .= str_replace(["[[TITLE]]","[[UPLOADER]]"], [$oRR->getFile(),$oRR->getUploader(true)['realname']], template);
    $counter++;
}

if($counter == 0){
    output("No New Resources Found");
}
else if($counter == 1){
    output("{$counter} New Resource Found");
}
else{
    output("{$counter} New Resources Found");
}

$bodyText = <<<BodyText
Hello CATS crew!
This is your bi-weekly email to let you know what has been uploaded to the back end.\n
The following resources are new or revised in the last 2 weeks (14 days):
[[RecordBody]]
Enjoy!
CATS Developer Team
BodyText;

$bodyText = str_replace("[[RecordBody]]",$recordBody, $bodyText);

$to = "cheshire@catherapyservices.ca";
if($counter > 0){
    SEEDEmailSend(["resources@catherapyservices.ca","CATS Resource System"], "", "New resources for you!", $bodyText,"",['reply-to'=>"developer@catherapyservices.ca","bcc"=>[$to]]);
    output("Email Sent");
}
else{
    output("Email Not Sent");
}