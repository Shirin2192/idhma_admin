$('#AnnouncementForm').on('submit', function (e) {
    e.preventDefault();

    // Clear previous error messages
    $('.error-text').text('');

    var form = $('#AnnouncementForm')[0];
    var formData = new FormData(form);

    $.ajax({
        url: frontend + controllerName + "/save_announcement",
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
                $('#AnnouncementTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    // showConfirmButton: true,
                    timer: 2000, // Auto-close after 2 seconds (2000 ms)
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                $('#AnnouncementForm')[0].reset();
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
                // Optional upload error fallback
                if (response.upload_error) {
                    $('#banner_error').text(response.upload_error);

                    // Hide the upload error message after 5 seconds
                    setTimeout(function() {
                        $('#banner_error').text('');
                    }, 5000);
                }
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
        }
    });
});
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#AnnouncementTable')) {
        $('#AnnouncementTable').DataTable().clear().destroy();
    }
    
    const table = $('#AnnouncementTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: frontend + controllerName + '/announcement_data_on_datatable',
            type: 'POST',

        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            // remove or replace the invalid blogs column
          
            {data: 'message'},
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info me-1 view-btn" title="View" data-id="${row.id}">
                            <i class="icon-eye menu-icon"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1 edit-btn" title="Edit" data-id="${row.id}">
                            <i class="icon-pencil menu-icon"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" title="Delete" data-id="${row.id}">
                           <i class="icon-trash menu-icon"></i>
                        </button>
                    `;
                }
            }
        ]
    });

// View button handler
$('#AnnouncementTable').on('click', '.view-btn', function () {
    const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute

    // Make the AJAX request to fetch blog details
    $.ajax({
        url: frontend + controllerName + '/announcement_data_on_id', // Corrected controller path
        type: 'POST',
        data: {
            id: id // Send the blog ID to fetch the details
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // If the response is successful, populate the modal with the blog data
                const AnnouncementData = response.data;
                $('#view_title').text(AnnouncementData.title); // Set the title
                $('#view_message').text(AnnouncementData.message); // Set the message

                // Show the modal with the blog details
                $('#viewAnnoucementModal').modal('show');
            } else {
                // Handle the error if the status is not success
                alert('Failed to load blog details.');
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            console.error('AJAX Error: ' + status + ' - ' + error);
            alert('An error occurred while fetching the blog details.');
        }
    });
});   

   // Edit button handler
$('#AnnouncementTable').on('click', '.edit-btn', function () {
    const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute

    // Make the AJAX request to fetch blog details
    $.ajax({
        url: frontend + controllerName + '/announcement_data_on_id', // Corrected controller path
        type: 'POST',
        data: {
            id: id // Send the blog ID to fetch the details
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // If the response is successful, populate the modal with the blog data
                const AnnouncementData = response.data;

                // Populate modal fields
                $('#edit_announcement_id').val(AnnouncementData.id);
                $('#edit_title').val(AnnouncementData.title);
                $('#edit_message').val(AnnouncementData.message);

                // Check the appropriate checkboxes
                $('input[name="edit_send_email"]').prop('checked', AnnouncementData.send_email == 1);
                $('input[name="edit_send_whatsapp"]').prop('checked', AnnouncementData.send_whatsapp == 1);
                $('#editAnnouncementModal').modal('show');
            } else {
                // Handle the error if the status is not success
                alert('Failed to load blog details.');
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            console.error('AJAX Error: ' + status + ' - ' + error);
            alert('An error occurred while fetching the blog details.');
        }
    });
});
// Delete button handler (Soft Delete)
$('#AnnouncementTable').on('click', '.delete-btn', function () {
    const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute

    // Show the delete confirmation modal
    $('#deleteBannerModal').modal('show');

    // Handle the confirm delete button click
    $('#confirmDeleteBtn').off('click').on('click', function () {
        $.ajax({
            url: frontend + controllerName + '/delete_banners', // Ensure correct controller path
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // Display success message using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Banner Deleted',
                        text: response.message,
                        timer: 2000, // Auto-close after 2 seconds (2000 ms)
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                    // Reload DataTable without resetting the page
                    table.ajax.reload(null, false);
                } else {
                    // Display error message if the delete failed
                    alert(response.message || 'Soft delete failed.');
                }

                // Close the modal after the action is complete
                $('#deleteBannerModal').modal('hide');
            },
            error: function () {
                alert('Error while soft deleting the blog.');
                // Close the modal after the error
                $('#deleteBannerModal').modal('hide');
            }
        });
    });
});


});
$('#editAnnouncementForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this); // Collect the form data, including the file
    $.ajax({
        url: frontend + controllerName + '/update_announcement', // Adjust this URL to your controller method
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
            // Clear previous errors
            $('#editAnnouncementForm small.text-danger').text('');
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#editAnnouncementModal').modal('hide');
                $('#editAnnouncementForm')[0].reset();

                // Refresh the table if needed
                $('#AnnouncementTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 2000, // Auto-close after 2 seconds (2000 ms)
                    // showConfirmButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

            } else if (response.status === 'error') {
                // Display validation errors
                if (response.errors) {
                    $.each(response.errors, function (key, value) {
                        $('#edit_' + key + '_error').text(value);
                    });
                } else {
                    alert('Something went wrong. Please try again.');
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('An unexpected error occurred.');
        }
    });
});
