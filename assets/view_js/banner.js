$('#bannerForm').on('submit', function (e) {
    e.preventDefault();

    // Clear previous error messages
    $('.error-text').text('');

    var form = $('#bannerForm')[0];
    var formData = new FormData(form);

    $.ajax({
        url: frontend + controllerName + "/save_banners",
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
                $('#BannerTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    // showConfirmButton: true,
                    timer: 2000, // Auto-close after 2 seconds (2000 ms)
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                $('#bannerForm')[0].reset();
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
    if ($.fn.DataTable.isDataTable('#BannerTable')) {
        $('#BannerTable').DataTable().clear().destroy();
    }
    
    const table = $('#BannerTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: frontend + controllerName + '/banners_data_on_datatable',
            type: 'POST',

        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            // remove or replace the invalid blogs column
          
            {
                data: 'banners',
                render: function (data) {
                    return '<img src="' + frontend + 'uploads/banners/' + data + '" alt="Banners" width="50" height="50" />';
                }
            },
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
    $('#BannerTable').on('click', '.view-btn', function () {
        const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute
    
        // Make the AJAX request to fetch blog details
        $.ajax({
            url: frontend + controllerName + '/banners_data_on_id', // Corrected controller path
            type: 'POST',
            data: {
                id: id // Send the blog ID to fetch the details
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // If the response is successful, populate the modal with the blog data
                    const BannerData = response.data;
                    $('#view_title').text(BannerData.title); // Set the title
                    if (BannerData.banners) {
                        $('#view_banners').attr('src', frontend + 'uploads/banners/' + BannerData.banners);
                    } else {
                        // Fallback image if no featured image is found
                        $('#view_banners').attr('src', '/path/to/default-image.jpg');
                    }    
                    $('#view_status').text(BannerData.status); // Set the status
    
                    // Show the modal with the blog details
                    $('#viewBannersModal').modal('show');
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
$('#BannerTable').on('click', '.edit-btn', function () {
    const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute

    // Make the AJAX request to fetch blog details
    $.ajax({
        url: frontend + controllerName + '/banners_data_on_id', // Corrected controller path
        type: 'POST',
        data: {
            id: id // Send the blog ID to fetch the details
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // If the response is successful, populate the modal with the blog data
                const BannerData = response.data;

                // Populate modal fields
                $('#edit_banner_id').val(BannerData.id);
                $('#edit_title').val(BannerData.title);
                $('#edit_current_banner').val(BannerData.banners); // Set the current banner name for reference
                       // Check if the blog has a featured image and set it
                if (BannerData.banners) {
                    $('#current_banner').attr('src', frontend + 'uploads/banners/' + BannerData.banners).show();
                } else {
                    $('#current_banner').hide();  // Hide the image section if there's no image
                }                // Show the modal with the blog details
                $('#editBannersModal').modal('show');
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
// Delete button handler (Soft Delete)
$('#BannerTable').on('click', '.delete-btn', function () {
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
$('#editBannerForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this); // Collect the form data, including the file
    $.ajax({
        url: frontend + controllerName + '/update_banners', // Adjust this URL to your controller method
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
            // Clear previous errors
            $('#editBannerForm small.text-danger').text('');
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#editBannersModal').modal('hide');
                $('#editBannerForm')[0].reset();
                $('#current_featured_image').hide();
                // Refresh the table if needed
                $('#BannerTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
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
