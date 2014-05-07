<!DOCTYPE html>
<?php
$dir    = 'notes/';
$files = scandir($dir);

?>
<html>
    <head>
               <meta charset="utf-8">
        <title>Notes</title>
        <!-- Load the jQuery UI CSS -->
        <link rel="stylesheet" href="css/dot-luv/jquery-ui-1.10.4.custom.css" type="text/css" />
         
        <!-- Load jQuery first before jQuery UI! -->
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
        <style>
            .dialog_form th {
                text-align: left;
            }
             
            .dialog_form textarea, .dialog_form input[type=text] {
                width: 320px;
            }

            .ui-widget {
                font-size: 0.8em;
            }
        </style>
    </head>
    <body id="body">

<!--<button id="create_button">Create a new window</button>!-->
<div id="menu" style="position: fixed; top: 20px; left: 10px;">
        <?php
        $menu = $dialogs = "";
        foreach($files as $file){
            if($file != "." && $file != ".." && substr($file,-1) != "~"){
                if(substr($file,0,2) != "h-"){
                    $name = substr($file,0,-4);
                    $menu .= "<span name='$name' id='li-$name' class='li-menu'><li>".$name."</li></span>";
                    $dialogs .= '<div style="display:none" id="'.$name.'" class="dialog_window" title="'.$name.'">
                                    <span id="edit-'.$name.'"><b>editar</b></span><br><br>
                                    <div id="note-content-'.$name.'">'.file_get_contents("notes/$name.txt").'</div>
                                    <div style="display:none" id="div-edit-'.$name.'">
                                        <textarea id="textarea-'.$name.'">
                                        </textarea><br><br>
                                        <button id="edit-button-'.$name.'">Guardar</button>
                                        <button id="cancel-button-'.$name.'">Cancelar</button>
                                    </div>
                                </div>';
                }
            }
        }
        ?>


    <ul>
        <?=$menu?>
    </ul>
</div>
<?=$dialogs?>
<div id="jsdiv" style="position: fixed; bottom: 0px;">
    <input type="text" name="jsinput" id="jsinput" />
    <button id="jssubmit" > submit </button>
</div>





<div id="dialog_window_1" class="dialog_window" title="Dialog window generator">
    <h3>Create a new <code>Dialog</code> Window</h3>
    <table class="dialog_form">
        <tr>
            <th>window Title</th>
        </tr>
        <tr>
            <td><input type="text" id="new_window_title" /></td>
        </tr>
        <tr>
            <th>
                window Content (Text, HTML, CSS, JS)
            </th>
        </tr>
        <tr>
            <td>
                <textarea id="new_window_content"></textarea>
            </td>
        </tr>
    </table>
</div>


<script>
    $(document).ready(function() {
        $("#jssubmit").button().click(function(){
            eval($("#jsinput").val());
        });
        /*$("#create_button").button().click(function() {
            var create_dialog = $("#dialog_window_1");
            var create_button = $(this);
             
            //if the window is already open, then close it and replace the label of the button
            if( create_dialog.dialog("isOpen") ) {
                create_button.button("option", "label", "Create a new window");
                create_dialog.dialog("close");
            } else {
                //otherwise, open the window and replace the label again
                create_button.button("option", "label", "Close window");
                create_dialog.dialog("open");
            }
        });*/
     
        //open our dialog window, but set autoOpen to false so it doesn"t automatically open when initialized

        $(".li-menu").each(function(){
            var dialogName = $(this).attr("name");
            $("#"+dialogName).dialog();
            $("#"+dialogName).dialog('close');
            $("#edit-button-"+dialogName).button();
            $("#cancel-button-"+dialogName).button();
            $("#edit-"+dialogName).click(function(){
                $("#textarea-"+dialogName).val($("#note-content-"+dialogName).html());
                $("#div-edit-"+dialogName).show();
                $("#note-content-"+dialogName).hide();
            });
            $("#edit-button-"+dialogName).click(function(){
                var content = $("#textarea-"+dialogName).val();
                $("#note-content-"+dialogName).html(content);
                $.ajax({
                    type: "POST",
                    url: "lib/updateWindow.php",
                    data: { name: dialogName, 
                            content: content }
                    })
                    .done(function( msg ) {
                        $("#div-edit-"+dialogName).hide();
                        $("#note-content-"+dialogName).show();
                });
            });

            $("#cancel-button-"+dialogName).click(function(){
                $("#div-edit-"+dialogName).hide();
                $("#note-content-"+dialogName).show();
            });

        });

        $(".li-menu").click(function(){
            var dialogName = $(this).attr("name");
            if($("#"+dialogName).dialog( "isOpen" )==true){
                $("#"+dialogName).dialog('close');

            }else{
                $("#"+dialogName).dialog('open');

            }

        });
        

        $('#dialog_window_1').dialog({
            width: 'auto',
            height: 'auto',
            position: { my: "right top", at: "right top", of: "body" },
            autoOpen : true,
            buttons: [
                {
                    text: 'Create',
                    click: function() {
                        //get the total number of existing dialog windows plus one (1)
                        var div_count = $('.dialog_window').length + 1;
                         
                        //generate a unique id based on the total number
                        var div_id = 'dialog_window_' + div_count;
                         
                        //get the title of the new window from our form, as well as the content
                        var div_title = $('#new_window_title').val();
                        var div_content = $('#new_window_content').val();
                         
                        //generate a buttons array based on which ones of our checkboxes are checked
                        var buttons = new Array();
                        if( $('#alertbutton').is(':checked') ) {
                            buttons.push({
                                text: 'ALERT',
                                click: function() {
                                    alert('ALERTING from Dialog Widnow: ' + div_title);
                                }
                            });
                        }
                         
                        if( $('#closebutton').is(':checked') ) {
                            buttons.push({
                                text: 'CLOSE',
                                click: function() {
                                    $('#' + div_id).dialog('close');
                                }
                            });
                        }
                         
                        //append the dialog window HTML to the body
                        $('body').append('<div class="dialog_window" id="' + div_id + '">' + div_content + '</div>');
                         
                        //initialize our new dialog
                        var dialog = $('#' + div_id).dialog({
                            width: 'auto',
                            height: 'auto',
                            title : div_title,
                            autoOpen : true,
                            buttons: buttons
                        });

                        $.ajax({
                            type: "POST",
                            url: "lib/createWindow.php",
                            data: { div_id: div_id, 
                                    name: div_title, 
                                    content: div_content }
                            })
                            .done(function( msg ) {
                            alert( "Data Saved: " + msg );
                        });

                    }
                }
            ]
        });
         
        //initialize our buttonset so our checkboxes are changed
        $("#buttonlist").buttonset();

        $("#dialog_window_1").on( "dialogdragstop", function( event, ui ) {
        console.log("left: "+ui.position.left);
        console.log("top: "+ui.position.top);

        } );

    });
</script>

    </body>
</html>