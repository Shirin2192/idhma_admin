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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Membership Benefits</a></li>
                    </ol>
                </div>
            </div>
            <!-- row -->

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="MemberBenefitsForm">
                                    <h4 class="card-title">Add Membership Benefits</h4>
                                    <div class="form-validation">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="title_benefits">Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title_benefits"
                                                    name="title_benefits" placeholder="Enter Title" value="<?= $membership_benefits['title_benefits'] ?>">
                                                    <small id="title_benefits_error" class="text-danger"></small>

                                            </div>
                                            <div class="col-lg-12">
                                                <label class="col-form-label" for="member_benefits">Member Benefits <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="member_benefits" id="member_benefits" class="form-control" rows="20"><?= $membership_benefits['benefits'] ?></textarea>
                                                <small class="text-danger" id="member_benefits_error"></small>
                                            </div>
                                        </div>
                                        <hr>
                                        <h3 class="card-title">Activities of the IHDMA</h3>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label class="col-form-label" for="title_activities">Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title_activities"
                                                    name="title_activities" placeholder="Enter Title" value="<?= $membership_benefits['title_activities'] ?>">
                                                    <small id="title_activities_error" class="text-danger"></small>
                                            </div>
                                            <div class="col-lg-12">
                                                <label class="col-form-label" for="activities_of_ihdma">Activities of the IHDMA <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="activities_of_ihdma" id="activities_of_ihdma" class="form-control" rows="20"><?= $membership_benefits['activities'] ?></textarea>
                                                <small class="text-danger" id="activities_of_ihdma_error"></small>
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
          
        </div>
        <!-- #/ container -->
        <!-- Modal Popup -->
        
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
    <script src="<?= base_url()?>assets/view_js/member_benefits.js"></script>
</body>

</html>