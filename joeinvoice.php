<?php

$raInvoice = array(
    'client-name' => "Joe Wildfong",
    'client-addr' => "68 Dunbar Rd South",
    'client-city' => "Waterloo",
    'client-prov' => "ON",
    'client-postcode' => "N2L 2E3",
    'invoice-date' => date( 'Y-m-d' ),
    'items' => array( array('Therapy provided by Sue Wahl', '1.0', '120.00' ),
                      array('Missed appointment charge', '', '100.00') )
    
);


echo DrawInvoice( $raInvoice );


function DrawInvoice( $ra )
{
    $sTemplate = 
<<<Invoice

<div class='container'>


    <div class='addressBlock'>
    [[client-name]]<br/>
    [[client-addr]]<br/>
    </div>

</div>

Invoice;
    
    
    $sTemplate = str_replace( "[[client-name]]", $ra['client-name'], $sTemplate );
    $sTemplate = str_replace( "[[client-addr]]", $ra['client-addr'], $sTemplate );
    
    return( $sTemplate );
}