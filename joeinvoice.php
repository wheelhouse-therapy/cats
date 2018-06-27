<?php

$raInvoice = array(
    'client-name' => "Joe Wildfong",
    'client-addr' => "68 Dunbar Rd South",
    'client-city' => "Waterloo",
    'client-prov' => "ON",
    'client-postcode' => "N2L 2E3",
    'invoice-date' => date( 'Y-M-d' ),
    'invoice-num' => "55",
    'email' => 'paris@catherapyservices.ca',
    'items' => array( array('Date', 'Designation', 'Hours', 'Amount'),
                      array('2018-Jan-14', 'Therapy provided by Sue Wahl', '1.0', '120.00' ),
                      array('2018-Mar-20', 'Missed appointment charge', '', '100.00') )
    
);


echo DrawInvoice( $raInvoice );


function DrawInvoice( $ra )
{
    $sTemplate = 
<<<Invoice

<style>
body {
    margin: 8px 15px;
}
#addressBlock {
    float: left;
    display: block;
}
#grid {
    display: grid;
    float: left;
    clear: both;
	grid-template-columns: 1.5fr 9fr 1fr 3fr;
    justify-content: space-evenly;
    border: 2px solid black;
    border-collapse: collapse;
    width: 100%;
}
.item {
    border-right: 1px solid black;
    background-color: inherit;
    padding: 5px;
}
* {
    font-family: Verdana, Tahoma, Geneva, sans-serif;
}
.wrapper {
    display: contents;
}
.wrapper:nth-child(even) {
    background-color: lightgrey;
}
.wrapper:nth-child(1) div.item {
    border-bottom: 2px solid black;
    text-transform: uppercase;
    text-align: center;
}
.item:nth-child(3) {
    text-align: center;
}
.item:nth-child(4) {
    text-align: right;
}
.total .item {
    border-top: 2px solid black;
}
#logo {
    height: 60px;
}
#thank-you {
    float: left;
    clear: both;
}
#invoice-details {
    float: right;
    display: block;
    width: 30%;
}
@media only screen and (max-width: 600px) {
    div#invoice-details {
        float: left;
        clear: both;
        width: 100%;
        margin-top: 20px;
    }
}
br {
    float: left;
    clear: both;
    display: block;
}
.line {
    box-sizing: border-box;
    height: 1px;
    border: 1px solid grey;
    width: 100%;
    display: inline-block;
}
.detail {
    padding: 10px;
    border: 1px solid black;
    border-radius: 10px;
    text-align: center;
    display: inline-block;
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
}
.detail.inline {
    box-sizing: border-box;
    width: 47%
}
#invoice-text {
    border: 1px solid black;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    background-color: #969696;
    font-weight: bold;
    margin-bottom: 10px;
}
span.bold {
    position: relative;
    bottom: 3px;
    font-weight: bold;
}
#thx-sign {
    grid-column: 1 / span 4;
    height: 100px;
    border-top: 2px solid black;
}
#thx-img {
    height: 100%;
    display: block;
    margin: auto;
}
</style>

<div class='container'>


    <div id='addressBlock'>
    <img src='w/img/CATS.png' id='logo'></img><br/>
    [[client-name]]<br/>
    [[client-addr]]<br/>
    [[client-city]] [[client-prov]] [[client-postcode]]
    </div>
    <div id='invoice-details'>
        <div id='invoice-text'>INVOICE</div>
        <div class='detail inline' style='float: left'><span class='bold'> Date </span><div class='line'></div> [[invoice-date]]</div>
        <div class='detail inline' style='float: right'><span class='bold'> Invoice # </span><div class='line'></div> [[invoice-num]]</div>
        <div class='detail'><span class='bold'> Payment Due </span><div class='line'></div> [[invoice-date]] </div>
    </div>
    <br/><br/><br/>
    <div id='grid'>
    [[items]]
    <div class='wrapper'><div class='item' id='thx-sign'><img src='w/img/thx-sign.png' alt='Thank You' id='thx-img'></div></div>
    <div class='wrapper total'><div class='item' style='grid-column: 1 / span 2; text-align: center;'> TOTALS:</div>
    <div class='item' style='text-align: center;'> [[hours]] </div><div class='item' style='text-align: right;'> [[total]] </div></div>
    </div> <br/> <br/>
    <span id='thank-you'>Thank you for your support. Payment is due by the end of the day. We accept cash, cheque or e-transfer to [[email]].</span>
    

</div>
Invoice;
    $t = "";
    $total = 0;
    $hours = 0;
    foreach ($ra['items'] as $r) {
        $t .= '<div class="wrapper"><div class="item">' . $r[0] . '</div><div class="item">' . $r[1] . '</div><div class="item">' . $r[2] . '</div><div class="item">' . $r[3] . '</div></div>';
        if ($r[3] == "Amount") {continue;}
        $total += $r[3];
        if (!$r[2]) {continue;}
        $hours += $r[2];
    }
    $sTemplate = str_replace( "[[items]]", $t, $sTemplate );
    $sTemplate = str_replace( "[[client-name]]", $ra['client-name'], $sTemplate );
    $sTemplate = str_replace( "[[client-addr]]", $ra['client-addr'], $sTemplate );
    $sTemplate = str_replace( "[[total]]", number_format((float)$total, 2, '.', ''), $sTemplate );
    $sTemplate = str_replace( "[[hours]]", number_format((float)$hours, 1, '.', ''), $sTemplate );
    $sTemplate = str_replace( "[[client-city]]", $ra['client-city'], $sTemplate );
    $sTemplate = str_replace( "[[client-prov]]", $ra['client-prov'], $sTemplate );
    $sTemplate = str_replace( "[[client-postcode]]", $ra['client-postcode'], $sTemplate );
    $sTemplate = str_replace( "[[invoice-date]]", $ra['invoice-date'], $sTemplate );
    $sTemplate = str_replace( "[[invoice-num]]", $ra['invoice-num'], $sTemplate );
    $sTemplate = str_replace( "[[email]]", $ra['email'], $sTemplate );
    
    
    return( $sTemplate );
}