<!DOCTYPE html>
<html lang="en">
   
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Loader -->
        <div id="loader-wrapper">
            <div id="loader">
                <div class="loader-ellips">
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                    <span class="loader-ellips__dot"></span>
                </div>
            </div>
        </div>
        <!-- /Loader -->

        <!-- Header -->
        <?php require_once __DIR__ . '/../layouts/head_section.php'; ?>
        <!-- /Header -->

        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="welcome-box">
                            <div class="welcome-img">
                                <img alt="" src="assets/img/profiles/avatar-02.jpg">
                            </div>
                            <div class="welcome-det">
                                <h3>Welcome, <?php echo ucwords($activeUserData['first_name']); ?>!</h3>
                                <p>
                                    <?php $date = date('l, d F Y');
                                    echo $date;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
