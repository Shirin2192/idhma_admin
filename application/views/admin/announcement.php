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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Announcement</a></li>
                    </ol>
                </div>
            </div>
            <!-- row -->

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="AnnouncementForm">
                                      <h4 class="card-title">Add Announcement</h4>
                                    <div class="form-validation">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="title">Title <span
                                                        class="text-danger">*</span> </label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    placeholder="Entre Title">
                                                    <small class="text-danger" id="title_error"></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="message">Message <span
                                                        class="text-danger">*</span> </label>
                                                <textarea type="text" class="form-control" id="message" name="message"
                                                    placeholder="Entre Message"></textarea>
                                                    <small class="text-danger" id="message_error"></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-form-label" for="content">Send via <span
                                                        class="text-danger">*</span> </label>
                                                <div><input type="checkbox" name="send_email" value="1"> Email<br>
                                                    <input type="checkbox" name="send_whatsapp" value="1">
                                                    WhatsApp<br><br>
                                                </div>
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
                                <h4 class="card-title">Announcement List</h4>
                                <div class="table-responsive">
                                    <table id="AnnouncementTable" class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Title</th>
                                                <th>Message</th>                                            
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
        <div class="modal fade" id="viewAnnoucementModal" tabindex="-1" role="dialog" aria-labelledby="viewAnnoucementModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAnnoucementModalLabel">View Announcement</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Title Section -->
                        <div class="form-group">
                            <label for="view_title" class="font-weight-bold">Title</label>
                            <p id="view_title" class="lead text-dark">Loading...</p>
                        </div>

                        <!-- Featured Image Section -->
                        <div class="form-group">
                            <label for="view_message" class="font-weight-bold">Message</label>
                            
                                <p id="view_message" class="lead text-dark">Loading...</p>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="editAnnouncementModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editAnnouncementForm">
                            <input type="hidden" id="edit_announcement_id" name="edit_announcement_id">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="col-form-label" for="edit_title">Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_title" name="edit_title"
                                        placeholder="Enter Title">
                                    <small class="text-danger" id="edit_title_error"></small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="edit_message" class="font-weight-bold">Message</label>
                                    <textarea  class="form-control" id="edit_message"
                                        name="edit_message"></textarea>
                                    <small class="text-danger" id="edit_message_error"></small>
                                </div>     
                                 <div class="col-md-6">
                                    <label class="col-form-label" for="content">Send via <span
                                            class="text-danger">*</span> </label>
                                    <div><input type="checkbox" name="edit_send_email" value="1"> Email<br>
                                        <input type="checkbox" name="edit_send_whatsapp" value="1">
                                        WhatsApp<br><br>
                                    </div>
                                     <small class="text-danger" id="edit_send_via_error"></small>
                                </div>                         
                            </div>
                            <!-- Submit Button -->
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <!-- For Bootstrap 4 -->
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteBannerModal" tabindex="-1" role="dialog" aria-labelledby="deleteBlogModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBlogModalLabel">Confirm Soft Delete</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to soft delete this Banner? This action cannot be undone.</p>
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
    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed & Developed by <a href="https://themeforest.net/user/quixlab">Quixlab</a>
                2018</p>
        </div>
    </div>
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
    <script src="<?= base_url()?>assets/view_js/announcement.js"></script>


</body>

</html>