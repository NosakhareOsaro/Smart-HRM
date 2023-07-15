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
                            <h3 class="page-title">Attendance</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Attendance</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <?php alert_display(); ?>
                <!-- Search Filter -->
                <form action="" method="POST">
                    <input type="hidden" name="action" value="CREATE" />
                    <?php csrf_field(); ?>
                    <div class="row filter-row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group form-focus">
                                <div class="cal-icon">
                                    <input name="date" type="text" class="form-control floating datetimepicker" id="attendance-date">
                                </div>
                                <label class="focus-label">Date</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group form-focus select-focus">
                                <select name="user_id" class="select floating" id="employee-select">
                                    <?php foreach ($users as $employee) : ?>
                                        <option value="<?= $employee['id'] ?>"><?= $employee['first_name'] . " " . $employee['last_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="focus-label">Select Employee</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group form-focus select-focus">
                                <select name="attendance_status" class="select floating" id="attendance-select">
                                    <option>-</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                </select>
                                <label class="focus-label">Mark Attendance</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <button class="btn btn-success btn-block" id="save-attendance" type="submit">Save</button>
                        </div>
                    </div>
                </form>
                <!-- /Search Filter -->

                <form action="" method="POST">
                    <?php csrf_field(); ?>
                    <!-- Search Filter -->
                    <input type="hidden" name="action" value="SEARCH" />
                    
                    <div class="row filter-row">
                    <div class="col-sm-6 col-md-3">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating" disabled>
								<label class="focus-label text-success">
                               <?php if ($attendanceRecords !== NULL && is_array($attendanceRecords) && isset($month) && isset($year)) {
                                    echo date('F', mktime(0, 0, 0, $month, 1))." ".$year;  
                                }else{
                                    echo "Month / Year";
                                }
                                ?>

                                </label>
							</div>
						</div>
                        <div class="col-sm-6 col-md-3">
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
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group form-focus select-focus">
                                <select name="year" class="select floating" required value="<?php echo (isset($_POST['year'])) ? $_POST['year'] : ''; ?>">
                                    <option>-</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                                <label class="focus-label">Select Year</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <button class="btn btn-success btn-block" id="save-attendance" type="submit"> Search</button>
                        </div>
                    </div>
                    <!-- /Search Filter -->
                </form>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <?php
                            if ($attendanceRecords !== null && is_array($attendanceRecords)) {
                                $monthDays = cal_days_in_month(CAL_GREGORIAN, $attendance_month, $attendance_year); // Replace with the desired month and year
                                $daysArray = range(1, $monthDays);

                                $tableHtml = '<table class="table table-striped custom-table table-nowrap mb-0">';
                                $tableHtml .= '<thead>';
                                $tableHtml .= '<tr>';
                                $tableHtml .= '<th>Employee</th>';

                                foreach ($daysArray as $day) {
                                    $tableHtml .= '<th>' . $day . '</th>';
                                }

                                $tableHtml .= '</tr>';
                                $tableHtml .= '</thead>';
                                $tableHtml .= '<tbody>';

                                $userAttendance = [];

                                foreach ($attendanceRecords as $record) {
                                    $user = $record['user_name'];
                                    $day = $record['day'];
                                    $status = $record['attendance_status'];

                                    if (!isset($userAttendance[$user])) {
                                        $userAttendance[$user] = array_fill(1, $monthDays, null);
                                    }

                                    $userAttendance[$user][$day] = $status;
                                }

                                foreach ($userAttendance as $user => $attendance) {
                                    $tableHtml .= '<tr>';
                                    $tableHtml .= '<td>';
                                    $tableHtml .= '<h2 class="table-avatar">';
                                    $tableHtml .= '<a class="avatar avatar-xs" href="profile.html"><img alt="" src="assets/img/profiles/avatar-09.jpg"></a>';
                                    $tableHtml .= '<a href="profile.html">' . $user . '</a>';
                                    $tableHtml .= '</h2>';
                                    $tableHtml .= '</td>';

                                    foreach ($daysArray as $day) {
                                        $attendanceStatus = $attendance[$day] == 0 ? '' : ($attendance[$day] == 1 ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>');
                                        $tableHtml .= '<td><a href="javascript:void(0);" data-toggle="modal" data-target="#attendance_info">' . $attendanceStatus . '</a></td>';
                                    }

                                    $tableHtml .= '</tr>';
                                }

                                $tableHtml .= '</tbody>';
                                $tableHtml .= '</table>';

                                echo $tableHtml;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
        </div>
        <!-- Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->
    <!-- jQuery -->
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>
