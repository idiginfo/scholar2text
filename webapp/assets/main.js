$(document).ready(function() {

    fixMainPos();
    $(window).resize(function() {
        fixMainPos();
    });

    //
    // File upload button functionality
    //
    $('#pdf-upload #pdffile-input').hide();
    $('#pdf-upload button[type=submit]').hide();
    $("#pdf-upload label[for='pdffile-input']").show();
    $("#pdf-upload label[for='pdffile-input']").css('cursor', 'pointer');
    $("#pdf-upload label[for='pdffile-input']").addClass('btn btn-primary');

    $("#pdf-upload label[for='pdffile-input']").click(function(e) {
        e.preventDefault();
        $('#pdf-upload #pdffile-input').click();
    });

    $('#pdf-upload #pdffile-input').change(function() {
        var fname = basename($(this).val());
        $('#pdf-upload').submit();
    });


    //PDF Upload Functionality
    $('#pdf-upload').ajaxForm({
        success: function(responseText, statusText, xhr, jq) {
            console.log(responseText);

            $('#left.pane').html("<iframe src='" + responseText.pdfurl + "'></iframe>")

            if (responseText.txt != '') {
                $('#right.pane').html("<textarea>" + responseText.txt + "</textarea>");
            }
            else {
                $('#right.pane').html("<p class='placeholder error'>Could not Parse the Document<br/><br/>Some are simply unparsable.</p>");
            }


            $.unblockUI();
        },
        error: function(jqXHR, textStatus, errorThrown) {

            //Kill the loading dialog
            $.unblockUI();
            var resp = $.parseJSON(jqXHR.responseText);

            //Build the message
            var msg = "<h1>There were errors during conversion</h1><ul>";
            $.each(resp.messages, function(k,v) {
                msg = msg + "<li>" + v + "</li>";
            });
            msg = msg + "</ul>";

            //Use blockUI to display errors
            $.blockUI({ message: msg, cursor: 'default', cursorReset: 'default', blockMsgClass: 'errorMsg'});
            $('.blockUI').click(function() { $.unblockUI(); });

        },
        beforeSubmit: function() {
            $.blockUI({ message: '<h1>Analyzing PDF...  Please wait</h1><p>(this can take a few moments)</p>' });
            $('.blockUI').click(function() { /* nuthin */ });
        }
    });

});

// ------------------------------------------------------------------

function fixMainPos()
{
    var topHeight = $('#top').outerHeight();
    $('#main').css('top', topHeight);
}

function basename(path, suffix) 
{
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ash Searle (http://hexmen.com/blog/)
    // +   improved by: Lincoln Ramsay
    // +   improved by: djmix
    // *     example 1: basename('/www/site/home.htm', '.htm');
    // *     returns 1: 'home'
    // *     example 2: basename('ecra.php?p=1');
    // *     returns 2: 'ecra.php?p=1'
    var b = path.replace(/^.*[\/\\]/g, '');

    if (typeof(suffix) == 'string' && b.substr(b.length - suffix.length) == suffix) {
        b = b.substr(0, b.length - suffix.length);
    }

    return b;
}