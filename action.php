<?php

//action.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'check_login')
	{
		if(isset($_SESSION['student_id']))
		{
			echo 'dashboard.php';
		}
		else
		{
			echo 'login.php';
		}
	}

	if($_POST['action'] == 'student_register')
	{
		$error = '';

		$success = '';

		$data = array(
			':student_email_address'	=>	$_POST["student_email_address"]
		);

		$object->query = "
		SELECT * FROM student_table 
		WHERE student_email_address = :student_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$student_verification_code = md5(uniqid());
			$data = array(
				':student_email_address'		=>	$object->clean_input($_POST["student_email_address"]),
				':student_password'				=>	$_POST["student_password"],
				':student_first_name'			=>	$object->clean_input($_POST["student_first_name"]),
				':student_last_name'			=>	$object->clean_input($_POST["student_last_name"]),
				':student_date_of_birth'		=>	$object->clean_input($_POST["student_date_of_birth"]),
				':student_gender'				=>	$object->clean_input($_POST["student_gender"]),
				':student_address'				=>	$object->clean_input($_POST["student_address"]),
				':student_phone_no'				=>	$object->clean_input($_POST["student_phone_no"]),
				':student_class_year'		=>	$object->clean_input($_POST["student_class_year"]),
				':student_added_on'				=>	$object->now,
				':student_verification_code'	=>	$student_verification_code,
				':email_verify'					=>	'Yes'
			);

			$object->query = "
			INSERT INTO student_table 
			(student_email_address, student_password, student_first_name, student_last_name, student_date_of_birth, student_gender, student_address, student_phone_no, student_class_year, student_added_on, student_verification_code, email_verify) 
			VALUES (:student_email_address, :student_password, :student_first_name, :student_last_name, :student_date_of_birth, :student_gender, :student_address, :student_phone_no, :student_class_year, :student_added_on, :student_verification_code, :email_verify)
			";

			$object->execute($data);

			require 'vendor/autoload.php';
			$mail = new PHPMailer(true);

			$mail->IsSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = '587';
			$mail->SMTPAuth = true;
			$mail->Username = 'frengkymanurung445@gmail.com';
			$mail->Password = 'frengkymanurung445';
			//$mail->SMTPSecure = '';
			$mail->From = 'frengkymanurung445@gmail.com';
			$mail->FromName = 'Admin';
			$mail->AddAddress($_POST["student_email_address"]);
			$mail->WordWrap = 50;
			$mail->IsHTML(true);
			$mail->Subject = 'Verification code for Verify Your Email Address';

			$message_body = '
			<p>For verify your email address, Please click on this <a href="'.$object->base_url.'verify.php?code='.$student_verification_code.'"><b>link</b></a>.</p>
			<p>Sincerely,</p>
			<p>Admin Website Booking</p>
			';
			$mail->Body = $message_body;

			if($mail->Send())
			{
				$success = '<div class="alert alert-success">Please Check Your Email for email Verification</div>';
			}
			else
			{
				$error = '<div class="alert alert-danger">' . $mail->ErrorInfo . '</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'student_login')
	{
		$error = '';

		$data = array(
			':student_email_address'	=>	$_POST["student_email_address"]
		);

		$object->query = "
		SELECT * FROM student_table 
		WHERE student_email_address = :student_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{

			$result = $object->statement_result();

			foreach($result as $row)
			{
				if($row["email_verify"] == 'Yes')
				{
					if($row["student_password"] == $_POST["student_password"])
					{
						$_SESSION['student_id'] = $row['student_id'];
						$_SESSION['student_name'] = $row['student_first_name'] . ' ' . $row['student_last_name'];
					}
					else
					{
						$error = '<div class="alert alert-danger">Wrong Password</div>';
					}
				}
				else
				{
					$error = '<div class="alert alert-danger">Please first verify your email address</div>';
				}
			}
		}
		else
		{
			$error = '<div class="alert alert-danger">Wrong Email Address</div>';
		}

		$output = array(
			'error'		=>	$error
		);

		echo json_encode($output);

	}

	if($_POST['action'] == 'fetch_schedule')
	{
		$output = array();

		$order_column = array('dosen_table.dosen_name', 'dosen_table.dosen_degree', 'dosen_table.dosen_expert_in', 'dosen_schedule_table.dosen_schedule_date', 'dosen_schedule_table.dosen_schedule_day', 'dosen_schedule_table.dosen_schedule_start_time');
		
		$main_query = "
		SELECT * FROM dosen_schedule_table 
		INNER JOIN dosen_table 
		ON dosen_table.dosen_id = dosen_schedule_table.dosen_id 
		";

		$search_query = '
		WHERE dosen_schedule_table.dosen_schedule_date >= "'.date('Y-m-d').'" 
		AND dosen_schedule_table.dosen_schedule_status = "Active" 
		AND dosen_table.dosen_status = "Active" 
		';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND ( dosen_table.dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_table.dosen_degree LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_table.dosen_expert_in LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY dosen_schedule_table.dosen_schedule_date ASC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["dosen_name"];

			$sub_array[] = $row["dosen_degree"];

			$sub_array[] = $row["dosen_expert_in"];

			$sub_array[] = $row["dosen_schedule_date"];

			$sub_array[] = $row["dosen_schedule_day"];

			$sub_array[] = $row["dosen_schedule_start_time"];

			$sub_array[] = '
			<div align="center">
			<button type="button" name="get_appointment" class="btn btn-primary btn-sm get_appointment" data-dosen_id="'.$row["dosen_id"].'" data-dosen_schedule_id="'.$row["dosen_schedule_id"].'">Get Appointment</button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);
	}


	if($_POST['action'] == 'fetch_schedule_student')
	{
		$output = array();

		$order_column = array('dosen_table.dosen_name', 'dosen_schedule_table.dosen_schedule_date', 'dosen_schedule_table.dosen_schedule_day', 'dosen_schedule_table.dosen_schedule_start_time', 'dosen_schedule_table.dosen_schedule_room');
		
		$main_query = "
		SELECT * FROM dosen_schedule_table 
		INNER JOIN dosen_table 
		ON dosen_table.dosen_id = dosen_schedule_table.dosen_id 
		";

		$search_query = '
		WHERE dosen_schedule_table.dosen_schedule_date >= "'.date('Y-m-d').'" 
		AND dosen_schedule_table.dosen_schedule_status = "Active" 
		AND dosen_table.dosen_status = "Active" 
		';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND ( dosen_table.dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY dosen_schedule_table.dosen_schedule_date ASC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["dosen_name"];

			$sub_array[] = $row["dosen_schedule_date"];

			$sub_array[] = $row["dosen_schedule_day"];

			$sub_array[] = $row["dosen_schedule_start_time"];

			$sub_array[] = $row["dosen_schedule_room"];

			$sub_array[] = '
			<div align="center">
			<button type="button" name="get_appointment" class="btn btn-primary btn-sm get_appointment" data-dosen_id="'.$row["dosen_id"].'" data-dosen_schedule_id="'.$row["dosen_schedule_id"].'">Get Appointment</button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);
	}

	
	if($_POST['action'] == 'edit_profile')
	{
		$data = array(
			':student_password'			=>	$_POST["student_password"],
			':student_first_name'		=>	$_POST["student_first_name"],
			':student_last_name'		=>	$_POST["student_last_name"],
			':student_date_of_birth'	=>	$_POST["student_date_of_birth"],
			':student_gender'			=>	$_POST["student_gender"],
			':student_address'			=>	$_POST["student_address"],
			':student_phone_no'			=>	$_POST["student_phone_no"],
			':student_class_year'	=>	$_POST["student_class_year"]
		);

		$object->query = "
		UPDATE student_table  
		SET student_password = :student_password, 
		student_first_name = :student_first_name, 
		student_last_name = :student_last_name, 
		student_date_of_birth = :student_date_of_birth, 
		student_gender = :student_gender, 
		student_address = :student_address, 
		student_phone_no = :student_phone_no, 
		student_class_year = :student_class_year 
		WHERE student_id = '".$_SESSION['student_id']."'
		";

		$object->execute($data);

		$_SESSION['success_message'] = '<div class="alert alert-success">Profile Data Updated</div>';

		echo 'done';
	}

	if($_POST['action'] == 'make_appointment')
	{
		$object->query = "
		SELECT * FROM student_table 
		WHERE student_id = '".$_SESSION["student_id"]."'
		";

		$student_data = $object->get_result();

		$object->query = "
		SELECT * FROM dosen_schedule_table 
		INNER JOIN dosen_table 
		ON dosen_table.dosen_id = dosen_schedule_table.dosen_id 
		WHERE dosen_schedule_table.dosen_schedule_id = '".$_POST["dosen_schedule_id"]."'
		";

		$dosen_schedule_data = $object->get_result();

		$html = '
		<h4 class="text-center">Student Details</h4>
		<table class="table">
		';

		foreach($student_data as $student_row)
		{
			$html .= '
			<tr>
				<th width="40%" class="text-right">Student Name</th>
				<td>'.$student_row["student_first_name"].' '.$student_row["student_last_name"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Contact No.</th>
				<td>'.$student_row["student_phone_no"].'</td>
			</tr>
			';
		}

		$html .= '
		</table>
		<hr />
		<h4 class="text-center">Appointment Details</h4>
		<table class="table">
		';
		foreach($dosen_schedule_data as $dosen_schedule_row)
		{
			$html .= '
			<tr>
				<th width="40%" class="text-right">Dosen Name</th>
				<td>'.$dosen_schedule_row["dosen_name"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Appointment Date</th>
				<td>'.$dosen_schedule_row["dosen_schedule_date"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Appointment Day</th>
				<td>'.$dosen_schedule_row["dosen_schedule_day"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Available Time</th>
				<td>'.$dosen_schedule_row["dosen_schedule_start_time"].' - '.$dosen_schedule_row["dosen_schedule_end_time"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Appointment Room</th>
				<td>'.$dosen_schedule_row["dosen_schedule_room"].'</td>
			</tr>
			';
		}

		$html .= '
		</table>';
		echo $html;
	}

	if($_POST['action'] == 'book_appointment')
	{
		$error = '';
		$data = array(
			':student_id'			=>	$_SESSION['student_id'],
			':dosen_schedule_id'	=>	$_POST['hidden_dosen_schedule_id']
		);

		$object->query = "
		SELECT * FROM appointment_table 
		WHERE student_id = :student_id 
		AND dosen_schedule_id = :dosen_schedule_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">You have already applied for appointment for this day, try for other day.</div>';
		}
		else
		{
			$object->query = "
			SELECT * FROM dosen_schedule_table 
			WHERE dosen_schedule_id = '".$_POST['hidden_dosen_schedule_id']."'
			";

			$schedule_data = $object->get_result();

			$object->query = "
			SELECT COUNT(appointment_id) AS total FROM appointment_table 
			WHERE dosen_schedule_id = '".$_POST['hidden_dosen_schedule_id']."' 
			";

			$appointment_data = $object->get_result();

			$total_dosen_available_minute = 0;
			$average_consulting_time = 0;
			$total_appointment = 0;

			foreach($schedule_data as $schedule_row)
			{
				$end_time = strtotime($schedule_row["dosen_schedule_end_time"] . ':00');

				$start_time = strtotime($schedule_row["dosen_schedule_start_time"] . ':00');

				$total_dosen_available_minute = ($end_time - $start_time) / 60;

				$average_consulting_time = $schedule_row["average_consulting_time"];
			}

			foreach($appointment_data as $appointment_row)
			{
				$total_appointment = $appointment_row["total"];
			}

			$total_appointment_minute_use = $total_appointment * $average_consulting_time;

			$appointment_time = date("H:i", strtotime('+'.$total_appointment_minute_use.' minutes', $start_time));

			$status = '';

			$appointment_number = $object->Generate_appointment_no();

			if(strtotime($end_time) > strtotime($appointment_time . ':00'))
			{
				$status = 'Booked';
			}
			else
			{
				$status = 'Waiting';
			}
			
			$data = array(
				':dosen_id'				=>	$_POST['hidden_dosen_id'],
				':student_id'				=>	$_SESSION['student_id'],
				':dosen_schedule_id'		=>	$_POST['hidden_dosen_schedule_id'],
				':appointment_number'		=>	$appointment_number,
				':reason_for_appointment'	=>	$_POST['reason_for_appointment'],
				':appointment_time'			=>	$appointment_time,
				':status'					=>	'Booked'
			);

			$object->query = "
			INSERT INTO appointment_table 
			(dosen_id, student_id, dosen_schedule_id, appointment_number, reason_for_appointment, appointment_time, status) 
			VALUES (:dosen_id, :student_id, :dosen_schedule_id, :appointment_number, :reason_for_appointment, :appointment_time, :status)
			";

			$object->execute($data);

			$_SESSION['appointment_message'] = '<div class="alert alert-success">Your Appointment has been <b>'.$status.'</b> with Appointment No. <b>'.$appointment_number.'</b></div>';
		}
		echo json_encode(['error' => $error]);
		
	}

	if($_POST['action'] == 'fetch_appointment')
	{
		$output = array();

		$order_column = array('appointment_table.appointment_number','dosen_table.dosen_name', 'dosen_schedule_table.dosen_schedule_date', 'appointment_table.appointment_time', 'dosen_schedule_table.dosen_schedule_day', 'appointment_table.status', 'appointment_table.appointment_approval');
		
		$main_query = "
		SELECT * FROM appointment_table  
		INNER JOIN dosen_table 
		ON dosen_table.dosen_id = appointment_table.dosen_id 
		INNER JOIN dosen_schedule_table 
		ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
		
		";

		$search_query = '
		WHERE appointment_table.student_id = "'.$_SESSION["student_id"].'" 
		';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND ( appointment_table.appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_table.dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR appointment_table.appointment_time LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR appointment_table.status LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY appointment_table.appointment_id ASC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["appointment_number"];

			$sub_array[] = $row["dosen_name"];

			$sub_array[] = $row["dosen_schedule_date"];			

			$sub_array[] = $row["appointment_time"];

			$sub_array[] = $row["dosen_schedule_day"];

			$status = '';

			if($row["status"] == 'Booked')
			{
				$status = '<span class="badge badge-warning">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'In Process')
			{
				$status = '<span class="badge badge-primary">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Completed')
			{
				$status = '<span class="badge badge-success">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Cancel')
			{
				$status = '<span class="badge badge-danger">' . $row["status"] . '</span>';
			}

			$sub_array[] = $status;

			$sub_array[] = $row["appointment_approval"];

			$sub_array[] = '<a href="download.php?id='.$row["appointment_id"].'" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>';

			$sub_array[] = '<button type="button" name="cancel_appointment" class="btn btn-danger btn-sm cancel_appointment" data-id="'.$row["appointment_id"].'"><i class="fas fa-times"></i></button>';

			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);
	}

	if($_POST['action'] == 'cancel_appointment')
	{
		$data = array(
			':status'			=>	'Cancel',
			':appointment_id'	=>	$_POST['appointment_id']
		);
		$object->query = "
		UPDATE appointment_table 
		SET status = :status 
		WHERE appointment_id = :appointment_id
		";
		$object->execute($data);
		echo '<div class="alert alert-success">Your Appointment has been Cancel</div>';
	}
}



?>