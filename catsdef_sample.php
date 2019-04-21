<?php

/* catsdef.php

   Copy this file to the directory above this one, and change the definitions below as needed.
   A lot of the time you can just use the defaults in _config.php so if those work you can delete the lines below.

   This file contains installation-specific definitions that should not be versioned --- your copied file will not be pushed to git.

   One reason why we don't put this info in git is that it can (and is) different on different peoples' machines, e.g. where CATSLIB and SEEDROOT live.

   Another reason is we don't ever want to put the db passwords in a versioned file, because everyone can see our files on github.
*/

define( "SEEDROOT",          "/home/cats/seeds/" );        // where you cloned the seeds repo, e.g. seeds/seedcore. Must have a trailing slash.
define( "CATSLIB",           "/home/cats/catslib/" );      // where you cloned the catslib repo. Must have a trailing slash
define( "CATSDIR_CONFIG",    "/home/cats/cats_config/" );  // where you make a dir to put config files in, e.g. google security files
define( "CATSDIR_LOG",       "/home/cats/cats_log/" );     // where you make a dir to put log files in
define( "CATSDIR_FILES",     "/home/cats/cats_files/" );   // where CATS will save files



/* Credentials for your cats database (assuming host==localhost)
 */
$config_KFDB = array(
    'cats'      => ['kfdbUserid' => 'cats',
                    'kfdbPassword' => 'cats',
                    'kfdbDatabase' => 'cats' ],
    'akaunting' => ['kfdbUserid' => 'cats',
                    'kfdbPassword' => 'cats',
                    'kfdbDatabase' => 'cats' ]
);

/**
 * @deprecated use $config_KFDB['cats'] instead
 * @var array $catsDefKFDB
 */
$catsDefKFDB = $config_KFDB['cats'];


/* Credentials for the email-to-Akaunting processor
 * as well as the email-resource processor
 */
$email_processor = array( 'receiptsEmail' => "Your Email",
                          'receiptsPSW'   => "Your Email PSW",
                          //'emailServer'   => "catherapyservices.ca"  //this is set in _config.php if you don't put it here. Use for dev access to alternate server name.
                          'akauntingUSR'  => "Your Akaunting USR",
                          'akauntingPSW'  => "Your Akaunting PSW",
                          'resourceEmail' => "Your Email",
                          'resourcePSW'   => "Your Email PSW"
                        );


/* Several file/dir locations are assumed in catslib/_config.php, e.g. CATSDIR_IMG but they can be overridden if you specify them below.
 * Put installation-specific definitions here, instead of in a cloned file, so they are not checked into git.
 */
//define( "CATSDIR_RESOURCES", CATSDIR_FILES."cats_resources/" );   // you can put these anywhere but this is a typical place

//define( "W_CORE", "../wcore/" );                        // should be relative to public_html/cats -- "../wcore" is the default in _config.php
//define( "W_CORE_URL", SEEDROOT."wcore/" );              // this has be under the www root, so you might have to copy it and change this


?>
