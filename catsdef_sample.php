<?php

/* catsdef.php

   Copy this file to the directory above this one, and change the definitions below as needed.

   This file contains installation-specific definitions that should not be versioned --- your copied file will not be pushed to git.

   One reason why we don't put this info in git is that it can (and is) different on different peoples' machines, e.g. where CATSLIB and SEEDROOT live.

   Another reason is we don't ever want to put the db passwords in a versioned file, because everyone can see our files on github.
*/

define( "SEEDROOT",       "/home/cats/seeds/" );        // where you cloned the seeds repo, e.g. seeds/seedcore. Must have a trailing slash.
define( "CATSLIB",        "/home/cats/catslib/" );      // where you cloned the catslib repo. Must have a trailing slash
define( "CATSDIR_CONFIG", "/home/cats/cats_config/" );  // where you make a dir to put config files in, e.g. google security files
define( "CATSDIR_LOG",    "/home/cats/cats_log/" );     // where you make a dir to put log files in
define( "CATSDIR_FILES",  "/home/cats/cats_files/" );   // where CATS will save files

define( "W_CORE_URL", SEEDROOT."wcore/" );              // this has be under the www root, so you might have to copy it and change this


$catsDefKFDB = array( 'kfdbUserid' => 'cats',               // credentials for your cats database (assuming host==localhost)
                      'kfdbPassword' => 'cats',
                      'kfdbDatabase' => 'cats' );

$email_processor = array( 'emailAccount' => "Your Email",
                          'emailPSW'     => "Your Email PSW",
                          'akauntingUSR' => "Your Akaunting USR",
                          'akauntingPSW' => "Your Akaunting PSW"
                        );


// Several file/dir locations are assumed in catslib/_config.php, e.g. CATSDIR_IMG but they can be overridden if you specify them below.
// Put installation-specific definitions here, instead of in a cloned file, so they are not checked into git.

?>
