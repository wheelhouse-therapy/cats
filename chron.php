<?php
require_once '_start.php';
require_once CATSLIB.'email_processor.php';
require_once CATSLIB.'AkauntingHook.php';

$processor = new EmailProcessor($email_processor['emailServer'],$email_processor['emailAccount'], $email_processor["emailPSW"]);
AkauntingHook::login($email_processor['akauntingUSR'],$email_processor['akauntingPSW']);
$processor->processEmails();
AkauntingHook::logout();