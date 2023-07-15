<?php

$activePage = 'attendance';

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
 $attendance_month=7; $attendance_year=2023;
// Proceed with the admin dashboard code here
// ...

$has_form_error = false;

$attendance = new Attendance();
	
if (($_SERVER['REQUEST_METHOD'] === 'POST') && $_POST['action'] == 'CREATE' && csrf_verify('post') === true) {
		// Get the form data
		$date = isset($_POST['date']) ? trim($_POST['date']) : null;
		$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : null;
        $attendance_status = isset($_POST['attendance_status']) ? trim($_POST['attendance_status']) : null;
	
		// Perform form validation
		if (empty($date) || empty($user_id) || empty($attendance_status)) {
			// Handle form validation errors
			alert_flash ('All field is required', 'error');
			$has_form_error = true;
		}else{
         

			

			$date = DateTime::createFromFormat("d/m/Y", $date);
			$date = $date->format("Y-m-d"); 
 
			$previousAttendance = $attendance->getByUserIdAndDate($user_id, $date);

			if($previousAttendance){
				$attendance_status = ($attendance_status === 'present') ? 1 : 2;

				$result = $attendance->updateAttendanceStatus($user_id, $date, $attendance_status);

				if ($result) {
					alert_flash('Attendance record updated', 'success');
	
				}else{
					alert_flash('Attendance record update failed', 'error');
					$has_form_error = true;
				}

			}else{
				$attendance_status = ($attendance_status === 'present') ? 1 : 2;

				$result = $attendance->create($user_id, $date, $attendance_status);

				if ($result) {
					alert_flash('Attendance Record Created', 'success');
	
				}else{
					alert_flash('Attendance creation failed', 'error');
					$has_form_error = true;
				}
			}
            


        }
	
		// Perform login authentication
		

	}else{
		
	}


	
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
		
			}else{

				alert_flash('No result found', 'error');
					$has_form_error = true;				
			}


		}
	}
