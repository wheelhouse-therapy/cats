<?php

/* Right way: we handle the POST, then return 303 which causes a GET with a slightly different url - which draws the form again.
 * The two-step process means the user won't re-post the form if they click Reload.
 */

session_start();

/* Wrong way: we handle the POST here, and allow the script to continue, drawing the form again.
 */
if( @$_REQUEST['foo'] == 'wrong' ) {
    echo "<p>You chose the wrong way. Your Post request was handled and the page was redrawn in the same script.</p>";
    echo "<p>Now click Reload and see what happens. The browser tries to re-submit your form! That could be dangerous, or at least irritating to the user. Also, using the Back button right now is sometimes a problem because of form re-submit.</p>";
} else
if( SEEDPRG() ) {
    echo "<p>You chose the {$_POST['foo']} way. Your Post request was submitted but the browser got a 303 response, which told it to do a GET on this page.</p>";
    echo "<p>Now click Reload and see what happens. All that does is repeat the GET, which is fine. Also, you can use the Back button and bookmark this page without weird re-submit behaviour.</p>";
}

$s1 = "<div><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='foo' value='wrong'/><input type='submit' value='Wrong Way'/></form></div>";

$s2 = "<div><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='foo' value='right'/><input type='submit' value='Right Way'/></form></div>";

echo "<table><tr><td>$s1</td><td>$s2</td></tr></table>";


function SEEDPRG()
/*****************
   Implement the Post, Redirect, Get paradigm for submitting forms.
   The purpose of this is to prevent the possibility of a user re-posting a form by reloading a page after a submit.

   Usage: Call this near the top of your script.
          If it returns true, process the contents of $_POST.
          Or you can ignore the return value and just check whether $_POST isn't empty.
 */
{
    $doPost = false;

    if( @$_SERVER['REQUEST_METHOD'] == 'POST' ) {
        // A form was submitted. Defer processing until the page is reloaded via 303, which causes the browser to do a GET on the given location.
        $uniqid = uniqid();
        $_SESSION['seedprg'][$uniqid] = $_POST;

        header( "Location: {$_SERVER['PHP_SELF']}?seedprgid=$uniqid", true, 303 );
        exit;
    } else
    if( ($uniqid = @$_REQUEST['seedprgid']) && isset($_SESSION['seedprg'][$uniqid]) ) {
        // A 303 was issued (by the code above) so the browser did a GET on the page.
        // Restore the deferred form parms and return true to tell the calling code to process $_POST now.
        $_POST = $_SESSION['seedprg'][$uniqid];
        unset( $_SESSION['seedprg'] );  // might as well get rid of the whole array because there shouldn't be multiple elements

        $doPost = true;
    }

    return( $doPost );
}

?>
