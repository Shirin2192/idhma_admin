$('#HBOTForm').on('submit', function (e) {
    e.preventDefault();

    // Clear previous error messages
    $('.error-text').text('');

    var form = $('#HBOTForm')[0];
    var formData = new FormData(form);

    $.ajax({
        url: frontend + controllerName + "/save_HBOT_Notices",
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
                $('#HBOTTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    // showConfirmButton: true,
                    timer: 2000, // Auto-close after 2 seconds (2000 ms)
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                $('#HBOTForm')[0].reset();
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
    if ($.fn.DataTable.isDataTable('#HBOTTable')) {
        $('#HBOTTable').DataTable().clear().destroy();
    }
    
 const table = $('#HBOTTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: frontend + controllerName + '/HBOT_Notices_data_on_datatable',
        type: 'POST',
        dataSrc: function (json) {
            return json.data || [];
        }
    },
    columns: [
        {
            data: null,
            title: 'Sr. No',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },
        { data: 'title', title: 'Title' },
        { data: 'description', title: 'Description' },
        {
            data: 'video_link',
            title: 'Video Link',
            render: function (data) {
                return data ? `<a href="${data}" target="_blank">Watch Video</a>` : 'No Link';
            }
        },
        {
            data: 'file_path',
            title: 'File',
            render: function (data) {
                return data
                    ? `<a href="${frontend}${data}" target="_blank">View PDF</a>`
                    : 'No file';
            }
        },
        {
            data: null,
            title: 'Action',
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
  $('#HBOTTable').on('click', '.view-btn', function () {
    const id = $(this).data('id'); // Get the journal PDF ID

    $.ajax({
        url: frontend + controllerName + '/HBOT_Notices_data_on_id',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;

                // Set title
                $('#view_title').text(data.title || 'No Title');

                // Set description (assumes an element with id="view_description" exists)
                $('#view_description').html(data.description || 'No Description');
                $('#view_button').html(data.button || 'No Button ');

                // Set video link (assumes an element with id="view_video_link" exists)
                if (data.video_link) {
                    $('#view_video_link')
                        .attr('href', data.video_link)
                        .text('Watch Video')
                        .show();
                } else {
                    $('#view_video_link')
                        .attr('href', '#')
                        .text('No Video Link')
                        .hide();
                }

                // Set file path
                if (data.file_path) {
                    const pdfUrl = frontend + data.file_path;
                    $('#view_pdfs_link')
                        .attr('href', pdfUrl)
                        .text('View PDF')
                        .show();
                } else {
                    $('#view_pdfs_link')
                        .attr('href', '#')
                        .text('No PDF Available')
                        .hide();
                }

                // Show modal
                $('#viewJournalPdfsModal').modal('show');
            } else {
                alert('Failed to load journal PDF details.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('An error occurred while fetching the journal PDF details.');
        }
    });
});

    
  $('#HBOTTable').on('click', '.edit-btn', function () {
    const id = $(this).data('id'); // Get the HBOT ID

    $.ajax({
        url: frontend + controllerName + '/HBOT_Notices_data_on_id',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;

                // Set form values
                $('#edit_hbot_id').val(data.id);
                $('#edit_title').val(data.title);
                $('#edit_description').val(data.description);
                $('#edit_link').val(data.video_link);
                $('#edit_button_name').val(data.button_name);
                $('#edit_current_file').val(data.file_path); // optional: only filename if needed
                $('#edit_button_name').val(data.button); // optional: only filename if needed

                // Update file link
                if (data.file_path) {
                    const fileUrl = frontend + data.file_path;
                    $('#edit_file_link')
                        .attr('href', fileUrl)
                        .text('View Current File')
                        .show();
                } else {
                    $('#edit_file_link')
                        .attr('href', '#')
                        .text('No File Available')
                        .hide();
                }

                // Show the modal
                $('#editHBOTModal').modal('show');
            } else {
                alert('Failed to load HBOT Notification details.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('An error occurred while fetching the HBOT details.');
        }
    });
});


$('#HBOTTable').on('click', '.delete-btn', function () {
    const id = $(this).data('id'); // Get the blog ID from the button's data-id attribute

    // Show the delete confirmation modal
    $('#deleteJournalPdfsModal').modal('show');

    // Handle the confirm delete button click
    $('#confirmDeleteBtn').off('click').on('click', function () {
        $.ajax({
            url: frontend + controllerName + '/delete_journal_pdf', // Ensure correct controller path
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // Display success message using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Journal PDFs Deleted',
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
                $('#deleteJournalPdfsModal').modal('hide');
            },
            error: function () {
                alert('Error while soft deleting the blog.');
                // Close the modal after the error
                $('#deleteJournalPdfsModal').modal('hide');
            }
        });
    });
});


});
$('#editHBOTForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this); // Collect the form data, including the file
    $.ajax({
        url: frontend + controllerName + '/update_HBOT_Notices_data', // Adjust this URL to your controller method
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
            // Clear previous errors
            $('#editHBOTForm small.text-danger').text('');
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#editHBOTModal').modal('hide');
                $('#editHBOTForm')[0].reset();
                // Refresh the table if needed
                $('#HBOTTable').DataTable().ajax.reload(null, false); // Reload without resetting pagination
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
