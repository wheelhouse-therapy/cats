<?php
require_once '_start.php';

const templateTEXT = <<<ResourceTextTemplate
[[TITLE]] --- Uploaded By: [[UPLOADER]]\n
ResourceTextTemplate;

const templateHTML = <<<ResourceHTMLTemplate
<a href='https://catherapyservices.ca/cats?screen=[[CABINET]]&dir=[[DIR]]'>[[TITLE]]</a>
		-- Uploaded By: [[UPLOADER]]<br />
ResourceHTMLTemplate;

function output(String $msg){
    echo "{$msg}<br />";
}

function getScreen(ResourceRecord $oRR){
    if(strtolower($oRR->getCabinet()) == "sop" || strtolower($oRR->getDirectory()) == "sop"){
        return "therapist-viewSOPs";
    }
    else if(strtolower($oRR->getCabinet()) == "reports" || strtolower($oRR->getDirectory()) == "reports"){
        return "therapist-reports";
    }
    else if(strtolower($oRR->getCabinet()) == "videos"){
        return "therapist-viewVideos";
    }
    else{
        return "therapist-filing-cabinet";
    }
}

$recordBody = "";
$recordHTML = "";

$counter = 0;

foreach (ResourceRecord::GetResourcesByNewness($oApp) as $oRR){
    $recordBody .= str_replace(["[[TITLE]]","[[UPLOADER]]"], [$oRR->getFile(),$oRR->getUploader(true)['realname']], templateTEXT);
    $recordHTML .= str_replace(["[[CABINET]]","[[DIR]]","[[TITLE]]","[[UPLOADER]]"], [getScreen($oRR),$oRR->getDirectory(),$oRR->getFile(),$oRR->getUploader(true)['realname']], templateHTML);
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

$bodyHTML = <<<BodyHTML
<!DOCTYPE html PUBLIC “-//W3C//DTD XHTML 1.0 Transitional//EN” “https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd”>
<html xmlns=“https://www.w3.org/1999/xhtml”>
	<head>
		<title>New resources for you!</title>
		<meta http–equiv=“Content-Type” content=“text/html; charset=UTF-8” />
		<meta http–equiv=“X-UA-Compatible” content=“IE=edge” />
		<meta name=“viewport” content=“width=device-width, initial-scale=1.0 “ />
	</head>
	<body>
		Hello CATS crew!<br />
		This is your weekly email to let you know what has been uploaded to the back end.<br /><br />
		The following resources are new or revised in the last week:<br />
		[[RecordHTML]]
		Enjoy!<br />
		CATS Developer Team
	</body>
</html>
BodyHTML;

$bodyText = <<<BodyText
Hello CATS crew!
This is your weekly email to let you know what has been uploaded to the back end.\n
The following resources are new or revised in the last week:
[[RecordBody]]
Enjoy!
CATS Developer Team
BodyText;

$bodyText = str_replace("[[RecordBody]]",$recordBody, $bodyText);
$bodyHTML = str_replace("[[RecordHTML]]",$recordHTML, $bodyHTML);

$to = "cheshire@catherapyservices.ca";
if($counter >= 0){
    SEEDEmailSend(["resources@catherapyservices.ca","CATS Resource System"], "", "New resources for you!", $bodyText,$bodyHTML,['reply-to'=>"developer@catherapyservices.ca","bcc"=>[$to]]);
    output("Email Sent");
}
else{
    output("Email Not Sent");
}