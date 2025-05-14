<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- theme meta -->
    <meta name="theme-name" content="quixlab" />

    <title>IHDMA - Dashboard</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Pignose Calender -->
    <?php include('common/css_files.php');?>

</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <?php include('common/nav_header.php');?>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <?php include('common/header.php');?>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <?php include('common/sidebar.php');?>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <div class="row page-titles mx-0">
                <div class="col p-md-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">HOBT Notification</a></li>
                    </ol>
                </div>
            </div>
            <!-- row -->

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="HBOTForm">
                                    <h4 class="card-title">HOBT Notification</h4>
                                    <div class="form-validation">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="title">Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    placeholder="Enter Title">
                                                <small class="text-danger" id="title_error"></small>

                                            </div>
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="description">description <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" id="description" name="description"
                                                    placeholder="Enter description"></textarea>
                                                <small class="text-danger" id="description_error"></small>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="link">Video Link <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="link" name="link">
                                                <small class="text-danger" id="link_error"></small>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="files">Upload File <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" class="form-control" id="files" name="files">
                                                <small class="text-danger" id="files_error"></small>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="files">Button Label <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="button_name"
                                                    name="button_name">
                                                <small class="text-danger" id="button_name_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
                                            <button type="submit" class="btn btn-success button_submit">Submit</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">HBOT Notification List</h4>
                                <div class="table-responsive">
                                    <table id="HBOTTable" class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Video Link</th>
                                                <th>File</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
        <!-- Modal Popup -->
        <!-- View Blog Details Modal -->
        <div class="modal fade" id="viewJournalPdfsModal" tabindex="-1" role="dialog"
            aria-labelledby="viewJournalPdfsLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewJournalPdfsLabel">View HBOT Notification</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Title Section -->
                        <div class="form-group">
                            <label for="view_title" class="font-weight-bold">Title</label>
                            <h5 id="view_title"></h5>
                        </div>
                        <!-- Featured Image Section -->
                        <div class="form-group">
                            <label for="view_description" class="font-weight-bold">Description</label>
                            <p id="view_description"></p>
                        </div>
                        <div class="form-group">
                            <label for="view_video_link" class="font-weight-bold">Watch Video</label>
                            <div><a id="view_video_link" href="#" target="_blank" style="display:none;">Watch
                                    Video</a><br></div>
                        </div>
                        <div class="form-group">
                            <label for="view_pdfs_link" class="font-weight-bold">PDF</label>
                            <div><a id="view_pdfs_link" href="#" target="_blank" style="display:none;">View PDF</a>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="view_button" class="font-weight-bold">Button Label</label>
                            <p id="view_button"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editHBOTModal" tabindex="-1" role="dialog"
            aria-labelledby="editHBOTModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editHBOTModalLabel">Edit HBOT Notification</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editHBOTForm" enctype="multipart/form-data">
                            <!-- Hidden fields -->
                            <input type="hidden" id="edit_hbot_id" name="edit_hbot_id">
                            <input type="hidden" id="edit_current_file" name="edit_current_file">

                            <div class="form-validation">
                                <div class="row">
                                    <!-- Title -->
                                    <div class="col-lg-6">
                                        <label for="edit_title" class="col-form-label">Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_title" name="edit_title"
                                            placeholder="Enter Title">
                                        <small class="text-danger" id="edit_title_error"></small>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-lg-6">
                                        <label for="edit_description" class="col-form-label">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="edit_description" name="edit_description"
                                            placeholder="Enter Description"></textarea>
                                        <small class="text-danger" id="edit_description_error"></small>
                                    </div>

                                    <!-- Video Link -->
                                    <div class="col-lg-6">
                                        <label for="edit_link" class="col-form-label">Video Link <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_link" name="edit_link"
                                            placeholder="Enter Video Link">
                                        <small class="text-danger" id="edit_link_error"></small>
                                    </div>

                                    <!-- Upload File -->
                                    <div class="col-lg-6">
                                        <label for="edit_file" class="col-form-label">Upload File <span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="edit_file" name="edit_file">
                                        <small class="text-danger" id="edit_file_error"></small>
                                    </div>

                                    <!-- Current File Preview -->
                                    <div class="col-12 text-center mt-3">
                                        <label class="font-weight-bold">Current Uploaded File</label><br>
                                        <a id="edit_file_link" href="#" target="_blank"
                                            class="btn btn-sm btn-secondary">View Current File</a>
                                    </div>

                                    <!-- Button Label -->
                                    <div class="col-lg-6 mt-3">
                                        <label for="edit_button_name" class="col-form-label">Button Label <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_button_name"
                                            name="edit_button_name" placeholder="Enter Button Label">
                                        <small class="text-danger" id="edit_button_name_error"></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Submit -->
                            <div class="modal-footer mt-3">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteJournalPdfsModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteBlogModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBlogModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to soft delete this Journal PDFs? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--**********************************
            Content body end
        ***********************************-->


    <!--**********************************
            Footer start
        ***********************************-->
    <?php include('common/footer.php');?>
    <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <?php include('common/js_files.php');?>
    <script src="<?= base_url()?>assets/view_js/hbot_notification.js"></script>
</body>

</html>