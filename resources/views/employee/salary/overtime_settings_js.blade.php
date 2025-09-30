// Wait for jQuery to be available
function waitForJQuery(callback) {
    if (window.jQuery) {
        callback(window.jQuery);
    } else {
        setTimeout(function() {
            waitForJQuery(callback);
        }, 50);
    }
}

waitForJQuery(function($) {
    console.log('Overtime settings JS loaded - jQuery available');

    $('#overtime_settings_form').on('submit', function (event) {
        event.preventDefault();
        console.log('Overtime settings form submitted via AJAX - preventing default submission');

    var formUrl = "/staff/employees/{{ $employee->id }}/overtime_settings_update";
    console.log('Submitting to URL:', formUrl);

    $.ajax({
        url: formUrl,
        method: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
            console.log(data);
            
            if (data.errors) {
                let errorMessages = '';
                for (var count = 0; count < data.errors.length; count++) {
                    errorMessages += data.errors[count] + '\n';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessages,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            }
            
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            }
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.success,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to update overtime settings. Please try again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        }

    });
});