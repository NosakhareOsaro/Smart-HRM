<!DOCTYPE html>
<html lang="en">

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
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
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Employee</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Employee</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee"><i class="fa fa-plus"></i> Add Employee</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row staff-grid-row">
                    <?php foreach ($users as $userData) : ?>
                        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                            <div class="profile-widget">
                                <div class="profile-img">
                                    <a href="profile.html" class="avatar"><img src="assets/img/profiles/avatar-02.jpg" alt=""></a>
                                </div>
                                <div class="dropdown profile-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit_employee"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_employee"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                                <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href="profile.html"><?php echo $userData['first_name'] . ' ' . $userData['last_name']; ?></a></h4>
                                <div class="small text-muted"><?php echo $userData['role']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- /Page Content -->
        </div>
        <!-- Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

    <!-- Add Employee Modal -->
    <div id="add_employee" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <?php alert_display(); ?>
                        <?php csrf_field(); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input class="form-control" type="text" name="first_name" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['first_name'])) ? $_POST['first_name'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input class="form-control" type="text" name="last_name" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['last_name'])) ? $_POST['last_name'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input class="form-control" type="email" name="email" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['email'])) ? $_POST['email'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input class="form-control" type="password" name="password" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['password'])) ? $_POST['password'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Confirm Password *</label>
                                    <input class="form-control" type="password" name="repeat_password" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['repeat_password'])) ? $_POST['repeat_password'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Phone *</label>
                                    <input class="form-control" type="text" name="phone" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['phone'])) ? $_POST['phone'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role *</label>
                                    <select class="form-control" name="role" required>
                                        <option value="admin" <?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="employee" <?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['role']) && $_POST['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Employee ID *</label>
                                    <input class="form-control" type="text" name="employee_id" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['employee_id'])) ? $_POST['employee_id'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary account-btn" type="submit">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Employee Modal -->
</body>

</html>
