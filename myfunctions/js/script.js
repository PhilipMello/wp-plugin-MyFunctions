jQuery(document).ready(function($) {
    $('#load-preset-btn').on('click', function() {
        var presetFile = $('#preset_file').val();
        
        if (presetFile === '') {
            alert('Please select a preset file.');
            return;
        }

        // Perform AJAX request to load the selected preset file
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'load_preset_file',
                preset_file: presetFile,
                _ajax_nonce: ajax_object.nonce
            },
            success: function(response) {
                // Append the loaded content to the textarea
                var currentContent = $('#functions_content').val();
                $('#functions_content').val(currentContent + '\n' + response);
            },
            error: function() {
                alert('Failed to load preset file.');
            }
        });
    });
});

jQuery(document).ready(function($) {
    var originalCode = $('#functions_content').val();
    
    // Change button color when code is modified
    $('#functions_content').on('input', function() {
        var newCode = $(this).val();
        if (newCode !== originalCode) {
            $('#myfunctions_save_button').css('background-color', 'red');
        } else {
            $('#myfunctions_save_button').css('background-color', '#0085ba'); // Default blue color
        }
    });
});