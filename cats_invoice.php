<?php

require_once "_start.php";
require_once CATSLIB."invoice/catsinvoice.php";

if( ($id = SEEDInput_Int( 'id' )) ) {
    CATSInvoice( $oApp, $id, "I" );    // mode I (inline) outputs the pdf in the browser
}

?>