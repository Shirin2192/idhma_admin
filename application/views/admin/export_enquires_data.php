<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- theme meta -->
    <meta name="theme-name" content="quixlab" />

    <title>IHDMA - Export Member</title>
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Export Enquiry List</a></li>
                    </ol>
                </div>
            </div>
            <!-- row -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Enquiry List</h4>
                                <div class="table-responsive">
                                    <table id="EnquiryDataTable" class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact No.</th>
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
         <!-- View Blog Details Modal -->
         <div class="modal fade" id="viewEnquiryModal" tabindex="-1" role="dialog" aria-labelledby="viewEnquiryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewEnquiryModalLabel">View Member Details</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Title Section -->
                            <div class="form-group col-md-6">
                                <label for="view_name" class="font-weight-bold">Name</label>
                                <p id="view_name" class="lead text-dark">Loading...</p>
                            </div>                           
                            <div class="form-group col-md-6">
                                <label for="view_email" class="font-weight-bold">Email</label>
                                <p id="view_email" class="text-muted">Loading...</p>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="view_contact" class="font-weight-bold">Contact</label>
                                <p id="view_contact" class="text-muted">Loading...</p>
                            </div>                           
                            <div class="form-group col-md-6">
                                <label for="view_message" class="font-weight-bold">Message</label>
                                <p id="view_message" class="text-justify">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editEnquiryModal" tabindex="-1" role="dialog" aria-labelledby="editEnquiryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEnquiryModalLabel">Edit Enquiry</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editMemberForm" enctype="multipart/form-data">
                            <input type="hidden" id="edit_member_id" name="edit_member_id">

                            <div class="row">
                                <!-- Title -->
                                <div class="form-group col-md-6">
                                    <label for="edit_name" class="font-weight-bold">Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="edit_name"
                                        placeholder="Enter Title">
                                    <small class="text-danger" id="edit_name_error"></small>
                                </div>

                                <!-- Slug -->
                                <div class="form-group col-md-6">
                                    <label for="edit_email" class="font-weight-bold">Email</label>
                                    <input type="text" class="form-control" id="edit_email" name="edit_email"
                                        placeholder="Enter Slug">
                                    <small class="text-danger" id="edit_email_error"></small>
                                </div>


                                <div class="form-group">
                                    <!-- Content (full width) -->
                                    <label for="edit_contact_no" class="font-weight-bold">Contact No</label>
                                    <textarea class="form-control" id="edit_contact_no" name="edit_contact_no" rows="5"
                                        placeholder="Enter Content"></textarea>
                                    <small class="text-danger" id="edit_contact_no_error"></small>
                                </div>

                            </div>
                            <!-- Status -->
                            <div class="form-group col-md-6">
                                <label for="edit_status" class="font-weight-bold">Status</label>
                                <select class="form-control" id="edit_status" name="edit_status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <small class="text-danger" id="edit_status_error"></small>
                            </div>

                            <!-- Submit Button -->
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <!-- For Bootstrap 4 -->
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                <button type="submit" class="btn btn-primary" form="editBlogForm">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteMemberModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMemberModalLabel">Confirm Soft Delete</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to soft delete this blog? This action cannot be undone.</p>
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
    <script src="<?= base_url()?>assets/view_js/enquiry.js"></script>

</body>

</html>