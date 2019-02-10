<?php
require_once '_start.php';
require_once CATSLIB.'email_processor.php';
require_once CATSLIB.'AkauntingHook.php';

$receiptsProcessor = new ReceiptsProcessor($email_processor['emailServer'],$email_processor['receiptsEmail'], $email_processor["receiptsPSW"]);
AkauntingHook::login($email_processor['akauntingUSR'],$email_processor['akauntingPSW']);
$receiptsProcessor->processEmails();
AkauntingHook::logout();
$resourcesProcessor = new ResourcesProcessor($email_processor['emailServer'],$email_processor['resourcesEmail'], $email_processor["resourcesPSW"]);
$resourcesProcessor->processEmails();