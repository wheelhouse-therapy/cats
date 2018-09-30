<?php

/* Right way: we handle the POST, then return 303 which causes a GET with a slightly different url - which draws the form again.
 * The two-step process means the user won't re-post the form if they click Reload.
 */
if( @$_REQUEST['foo'] == 'right' ) {
    // code would handle the posted form parameters here...

    // now respond with 303 instead of 200. This causes the browser to do a GET on the given location
    header( "Location: {$_SERVER['PHP_SELF']}?foo=didright", true, 303 );
    exit;
}


if( @$_REQUEST['foo'] == 'didright' ) {
    echo "<p>You chose the right way. Your Post request was submitted but the browser got a 303 response, which told it to do a GET on this page.</p>";
    echo "<p>Now click Reload and see what happens. All that does is repeat the GET, which is fine. Also, you can use the Back button and bookmark this page without weird re-submit behaviour.</p>";
}

/* Wrong way: we handle the POST here, and allow the script to continue, drawing the form again.
 */
if( @$_REQUEST['foo'] == 'wrong' ) {
    echo "<p>You chose the wrong way. Your Post request was handled and the page was redrawn in the same script.</p>";
    echo "<p>Now click Reload and see what happens. The browser tries to re-submit your form! That could be dangerous, or at least irritating to the user. Also, using the Back button right now is sometimes a problem because of form re-submit.</p>";
}
















$s1 = "<div><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='foo' value='wrong'/><input type='submit' value='Wrong Way'/></form></div>";

$s2 = "<div><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='foo' value='right'/><input type='submit' value='Right Way'/></form></div>";

echo "<table><tr><td>$s1</td><td>$s2</td></tr></table>";

?>
