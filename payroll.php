<?php
require_once '_start.php';

const MESSAGE = <<<Message
Message;

if(php_sapi_name() !== 'cli'){
    die("Can only be run by cli");
}