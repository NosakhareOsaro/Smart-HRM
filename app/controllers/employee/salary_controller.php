<?php

$activePage = 'salary';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . base_url('/logout'));
    exit;
}

$userID = trim($_SESSION['user_id']);
$user = new User();
$activeUserData = $user->get($userID);


// Verify user data and role
if ($activeUserData === false || !is_array($activeUserData) || $activeUserData['role'] !== 'admin') {
    header('Location: ' . base_url('/logout'));
    exit;
}

$users = $user->getAll();

$attendanceRecords =[];
$userData=[];
$attendance_month=7; $attendance_year=2023;
$salaries = array();
// Proceed with the admin dashboard code here
// ...

$has_form_error = false;

$attendance = new Attendance();
	

if (($_SERVER['REQUEST_METHOD'] === 'POST') && $_POST['action'] == 'SEARCH' && csrf_verify('post') === true) {
	
			
			$month 		= (isset($_POST['month'])) ? trim($_POST['month']) : null;
			
			$year 		= (isset($_POST['year'])) ? trim($_POST['year']) : null;


				// Perform form validation
		if (empty($month) || empty($year)) {
			// Handle form validation errors
			alert_flash ('All field is required4', 'error');
			$has_form_error = true;
		}else{
			
			$result = $attendance->search($month, $year);

			if($result){
				$attendanceRecords = $result;
				$attendance_month= $month; $attendance_year=$year;
                // Retrieve all staff
                $userData = $user->getAll();

                // Calculate base salary, pay per day, and absent days
                $baseSalary = 10000;
                
                
                // Calculate working days in the month
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $workingDays = 0;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $weekday = date('N', strtotime($date));
                    if ($weekday <= 5) {
                    $workingDays++;
                    }
                }

                // Calculate salary for each employee
                $salaries = array();
                foreach ($userData as $staff) {
                    $absentDays = 0;
                    foreach ($attendanceRecords as $record) {
                    if ($record['user_name'] == $staff['first_name']) {
                        $absentDays += ($record['attendance_status'] == 2) ? 1 : 0;
                    }
                    }
                    $workedDays = $workingDays - $absentDays;
                    $salary = ($workedDays / $workingDays) * $baseSalary;
                    $salaries[$staff['first_name']] = $salary;
                }




/* foreach ($userData as $staff) {
                    $absentDays = 0;
                    foreach ($attendanceRecords as $record) {
                    if ($record['user_name'] == $staff['first_name']) {
                        $absentDays += ($record['attendance_status'] == 2) ? 1 : 0;
                    }
                    }
                    $salary = $baseSalary - ($absentDays * $payPerDay);
                    $salaries[$staff['first_name']] = $salary;
                }


 * 
 */



			}else{

				alert_flash('No result found', 'error');
					$has_form_error = true;				
			}


		}
	}
