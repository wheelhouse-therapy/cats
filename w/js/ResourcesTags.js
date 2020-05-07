/* ResourcesTags.js
 *
 * JS for the tags that filter files in the Filing Cabinet
 */
 
 $(document).ready( function() {
    $('.resources-tag-new').click( function() {
        /* If someone clicks on the new tag button while adding another tag
         * The Old behavior caused the old tag to not be submitted.
         * This method prevents that from hapening
         */
        $('.resources-tag-new-form').each( function() {
            $(this).submit();
        });
        /* The [+] new-tag button opens an input control where the user can type a new tag
         */
        let formNew = $("<form class='resources-tag-new-form'>"
                       +"<input class='resources-tag-new-input resources-tag' type='text' value='' placeholder='New tag' />"
                       +"</form>" );

        /* Put the new-tag form after the [+] button and put focus on its input.
         */
        formNew.insertAfter($(this));
        formNew.find('.resources-tag-new-input').focus();
        
        /* When the user types something and hits Enter, send their text to the server and draw the new tag
         * (it will be drawn by the server when the page is refreshed).
         */
        formNew.submit( function(e) {
            e.preventDefault();
            let tag = $(this).find('input').val();
            let id = $(this).parent().data('id');
            
            if( tag && id ) {
                SEEDJXAsync2( "jx.php", 
                              { cmd:"resourcestag--newtag", 
                                id:id, 
                                tag:tag }, 
                              function(){}, function(){} );

                /* Todo: this puts the tag in place, and it should look the same when the server draws it on the next page refresh.
                         But, this is putting the tag into the <form> which isn't there after the page refresh so the spacing is just a little off.
                         Replace the <form> with the div.resources-tag, not just its innerhtml.
                 */
                $(this).html("<div class='resources-tag resources-tag-filled'>"+tag+"</div>");
            }
        });
    });

    /* When you click on a tag (not a new [+] button though) a delete control is inserted to the right. 
     * When you click on that, the tag you clicked is deleted.
     */ 
    $('.resources-tag-filled').click( function() {
        let deleteButton = $("<button>Delete</button>");
        deleteButton.insertAfter($(this));
        let saveThis = $(this);
        deleteButton.click( function() {
            let tag = saveThis.html();
            let folder = saveThis.parent().data('folder');
            let filename = saveThis.parent().data('filename');
            
            if( tag && folder && filename ) {
                SEEDJXAsync2( "jx.php", 
                              { cmd:"resourcestag--deletetag", 
                                folder:folder, 
                                filename:filename, 
                                tag:tag }, 
                              function(){}, function(){} );
            
                saveThis.remove();
                $(this).remove();
            }
        }); 
    });

});
