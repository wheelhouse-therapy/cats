<?php
if( class_exists( 'Imagick' ) ) {
    // Imagick class is installed
    echo "Imagick Extension installed<br />";
}
else{
    echo "Imagick Extension not installed<br />";
}
phpinfo();