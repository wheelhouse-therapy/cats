<?php

/* Output an invoice in a chosen format.
 *
 * id  = invoice number
 * fmt = html || pdf (default)
 */

require_once "_start.php";
require_once CATSLIB."invoice/catsinvoice.php";

/* N.B.
 *
 * CATSInvoice will not expose any invoice information unless the current user has permission to view that information.
 *
 * This is crucial because anyone can issue a request to this file and all they need is an invoice number.
 * Since the numbers are sequential it is easy to guess how to request someone else's invoice containing confidential information!
 */

if( !($id = SEEDInput_Int( 'id' )) ) goto done;
$oInvoice = new CATSInvoice( $oApp, $id );

switch( SEEDInput_Str( 'fmt' ) ) {
    default:
    case 'pdf':
        $oInvoice->InvoicePDF( "I" );    // mode I (inline) outputs the pdf in the browser
        break;
    case 'html':
        $oInvoice->InvoiceHTML();
        break;
}

done:
?>