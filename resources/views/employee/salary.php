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
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Salary</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Salary</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <?php alert_display(); ?>

                <form action="" method="POST">
                    <?php csrf_field(); ?>
                    <!-- Search Filter -->
                    <input type="hidden" name="action" value="SEARCH" />
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group form-focus select-focus">
                                <select name="month" class="select floating" required value="<?php echo (isset($_POST['month'])) ? $_POST['month'] : ''; ?>">
                                    <option>-</option>
                                    <?php $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
                                    foreach ($months as $value => $label) : ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="focus-label">Select Month</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group form-focus select-focus">
                                <select name="year" class="select floating" required value="<?php echo (isset($_POST['year'])) ? $_POST['year'] : ''; ?>">
                                    <option>-</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                </select>
                                <label class="focus-label">Select Year</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <button class="btn btn-success btn-block" id="save-attendance" type="submit"> Search</button>
                        </div>
                    </div>
                    <!-- /Search Filter -->
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table datatable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Employee ID</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Salary</th>
                                        <th>Payslip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($attendanceRecords !== NULL && is_array($attendanceRecords)) {
                                        foreach ($userData as $staff) { ?>
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="profile.html" class="avatar"><img alt="" src="assets/img/profiles/avatar-02.jpg"></a>
                                                        <a href="profile.html"><?php echo $staff['first_name']; ?></a>
                                                    </h2>
                                                </td>
                                                <td><?php echo $staff['id']; ?></td>
                                                <td><?php echo date('F', mktime(0, 0, 0, $month, 1)); ?></td>
                                                <td><?php echo $year; ?></td>
                                                <td><?php echo '$' . number_format($salaries[$staff['first_name']], 2); ?></td>
                                                <td><a class="btn btn-sm btn-primary" href="">Generate Slip</a></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

</body>

</html>
