ClassicEditor
    .create(document.querySelector('#member_benefits'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', '|',
            'fontColor', 'fontBackgroundColor', 'fontSize', '|',
            'link', 'bulletedList', 'numberedList', '|',
            'alignment', 'blockQuote', '|',
            'undo', 'redo'
        ],
       
        fontSize: {
            options: [ 'tiny', 'small', 'default', 'big', 'huge' ]
        },
        alignment: {
            options: [ 'left', 'center', 'right', 'justify' ]
        }
    })
    .catch(error => {
        console.error(error);
    });
ClassicEditor
.create(document.querySelector('#activities_of_ihdma'), {
    toolbar: [
        'heading', '|',
        'bold', 'italic', 'underline', '|',
        'fontColor', 'fontBackgroundColor', 'fontSize', '|',
        'link', 'bulletedList', 'numberedList', '|',
        'alignment', 'blockQuote', '|',
        'undo', 'redo'
    ],
    fontSize: {
        options: [ 'tiny', 'small', 'default', 'big', 'huge' ]
    },
    alignment: {
        options: [ 'left', 'center', 'right', 'justify' ]
    }
}).catch(error => {
    console.error(error);
});

$('#MemberBenefitsForm').on('submit', function (e) {
    e.preventDefault();

    // Clear previous error messages
    $('.error-text').text('');

    var form = $('#MemberBenefitsForm')[0];
    var formData = new FormData(form);

    $.ajax({
        url: frontend + controllerName + "/save_update_member_benefits",
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
               
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    // showConfirmButton: true,
                    timer: 2000, // Auto-close after 2 seconds (2000 ms)
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                $('#MemberBenefitsForm')[0].reset();
                location.reload(); // Reload the page after 2 seconds
                setTimeout(function() {
                    location.reload();
                }, 2000); // Reload the page after 2 seconds (2000 ms)
            } else if (response.status === 'error') {
                // Show validation errors under each input
                if (response.errors) {
                    $.each(response.errors, function (key, val) {
                        $('#' + key + '_error').text(val);
                        
                        // Hide the error message after 5 seconds
                        setTimeout(function() {
                            $('#' + key + '_error').text('');
                        }, 5000);
                    });
                }
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
        }
    });
});

