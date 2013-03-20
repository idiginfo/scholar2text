$(document).ready(function() {

    $('#pdf-upload').ajaxForm({
        success: function(responseText, statusText, xhr, jq) {
            console.log(responseText);

            $('#left.pane').html("<iframe src='" + responseText.pdf + "'></iframe>")

            if (responseText.txt != '') {
                $('#right.pane').html("<textarea>" + responseText.txt + "</textarea>");
            }
            else {
                $('#right.pane').html("<p class='placeholder error'>Could not Parse the Document<br/><br/>Some are simply unparsable.</p>");
            }


            $.unblockUI();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            var resp = $.parseJSON(jqXHR.responseText);

            var errorMsg = '<span class="upload-error"><h2>Error Processing Submission</h2><ul>';
            $.each(resp.messages, function(k, v) {
                errorMsg = errorMsg + "<li>" + v + "</li>";
            });
            errorMsg = errorMsg + "</ul><p>Click outside this box to continue</p></span>";

            $.unblockUI();
            $.blockUI({
                message: errorMsg,
                onOverlayClick: $.unblockUI,
                onClick: $.unblockUI
            });

            // console.log(responseText);
        },
        beforeSubmit: function() {
            $.blockUI({ message: '<h1>Analyzing PDF...  Please wait</h1><p>(this can take a few moments)</p>' });
            $('.blockUI').click(function() { /* nuthin */ });
        }
    });

});