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
<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
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
    height: 150px;
}
#thank-you {
    float: left;
    clear: both;
    margin-bottom: 10px;
}
#invoice-details {
    float: right;
    display: block;
    width: 30%;
}
@media screen and (max-width: 700px) {
    #invoice-details {
        float: left;
        clear: both;
        width: 100%;
        margin-top: 20px;
    }
    #addressBlock {
        float: none;
        width: 180px;
        margin: auto;
    }
}
@media screen and (max-width: 400px) {
    .wrapper:nth-child(1) div.item span {
        transform: rotate(90deg);
        display: inline-block;
        position: relative;
        text-align: center;
        top: 40%;
        width: 100%;
        height: 100%;
    }
    #grid {
        grid-template-rows: 150px;
        grid-template-columns: auto auto 35px auto;
    }
    .item {
        padding: 2px;
    }
    body {
        margin: 5px;
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
    <br/><br/>

</div>
<script>
function myFunction(x) {
    var toChange = document.querySelectorAll("div.item:nth-child(1)");
    if (x.matches) {
        var y = document.querySelectorAll("div.item:nth-child(1):not(#thx-sign) span").values();
        for (var i of y) {
            if(i.innerHTML.search(/^\d{4}-\w{3}-\d{2}$/i) === -1) {continue;}
            i.innerHTML = i.innerHTML.substring(2);
        }
    } else {
        var z = document.querySelectorAll("div.item:nth-child(1):not(#thx-sign) span").values();
        for (var i of z) {
            if(i.innerHTML.search(/^\d{2}-\w{3}-\d{2}$/i) === -1) {continue;}
            i.innerHTML = [[start-of-year]] + i.innerHTML;
        }
    }
}

var x = window.matchMedia("(max-width: 400px)");
myFunction(x);
x.addListener(myFunction);
</script>
Invoice;
    $t = "";
    $total = 0;
    $hours = 0;
    foreach ($ra['items'] as $r) {
        $t .= '<div class="wrapper">
                <div class="item"><span>' . $r[0]. '</span></div>
                <div class="item"><span>' . $r[1]. '</span></div>
                <div class="item"><span>' . $r[2] . '</span></div>
                <div class="item"><span>' . $r[3] . '</span></div>
            </div>';
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
    $sTemplate = str_replace( "[[start-of-year]]", substr(date("Y"), 0, 2), $sTemplate);
    
    
    
    return( $sTemplate );
}