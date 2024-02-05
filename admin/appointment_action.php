<?php

//appointment_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Dosen')
		{
			$order_column = array('appointment_table.appointment_number', 'student_table.student_first_name', 'dosen_table.dosen_name', 'dosen_schedule_table.dosen_schedule_date', 'appointment_table.appointment_time', 'dosen_schedule_table.dosen_schedule_day', 'appointment_table.status');
			$main_query = "
			SELECT * FROM appointment_table  
			INNER JOIN dosen_table 
			ON dosen_table.dosen_id = appointment_table.dosen_id 
			INNER JOIN dosen_schedule_table 
			ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
			INNER JOIN student_table 
			ON student_table.student_id = appointment_table.student_id 
			";

			$search_query = '';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'WHERE dosen_schedule_table.dosen_schedule_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" AND (';
			}
			else
			{
				$search_query .= 'WHERE ';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'appointment_table.appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_table.dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.appointment_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.status LIKE "%'.$_POST["search"]["value"].'%" ';
			}
			if($_POST["is_date_search"] == "yes")
			{
				$search_query .= ') ';
			}
			else
			{
				$search_query .= '';
			}
		}
		else
		{
			$order_column = array('appointment_table.appointment_number', 'student_table.student_first_name', 'dosen_schedule_table.dosen_schedule_date', 'appointment_table.appointment_time', 'dosen_schedule_table.dosen_schedule_day', 'appointment_table.status');

			$main_query = "
			SELECT * FROM appointment_table 
			INNER JOIN dosen_schedule_table 
			ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
			INNER JOIN student_table 
			ON student_table.student_id = appointment_table.student_id 
			";

			$search_query = '
			WHERE appointment_table.dosen_id = "'.$_SESSION["admin_id"].'" 
			';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'AND dosen_schedule_table.dosen_schedule_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" ';
			}
			else
			{
				$search_query .= '';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'AND (appointment_table.appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.appointment_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.status LIKE "%'.$_POST["search"]["value"].'%") ';
			}
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY appointment_table.appointment_id DESC ';
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

			$sub_array[] = $row["student_first_name"] . ' ' . $row["student_last_name"];

			if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = $row["dosen_name"];
			}
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

			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["appointment_id"].'"><i class="fas fa-eye"></i></button>
			&nbsp;
			
			<button type="button" name="cancel_appointment" class="btn btn-danger btn-circle btn-sm cancel_button" data-id="'.$row["appointment_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM appointment_table 
		WHERE appointment_id = '".$_POST["appointment_id"]."'
		";

		$appointment_data = $object->get_result();

		foreach($appointment_data as $appointment_row)
		{

			$object->query = "
			SELECT * FROM student_table 
			WHERE student_id = '".$appointment_row["student_id"]."'
			";

			$student_data = $object->get_result();

			$object->query = "
			SELECT * FROM dosen_schedule_table 
			INNER JOIN dosen_table 
			ON dosen_table.dosen_id = dosen_schedule_table.dosen_id 
			WHERE dosen_schedule_table.dosen_schedule_id = '".$appointment_row["dosen_schedule_id"]."'
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
					<th width="40%" class="text-right">student Name</th>
					<td>'.$student_row["student_first_name"].' '.$student_row["student_last_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Contact No.</th>
					<td>'.$student_row["student_phone_no"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Address</th>
					<td>'.$student_row["student_address"].'</td>
				</tr>
				';
			}

			$html .= '
			</table>
			<hr />
			<h4 class="text-center">Appointment Details</h4>
			<table class="table">
				<tr>
					<th width="40%" class="text-right">Appointment No.</th>
					<td>'.$appointment_row["appointment_number"].'</td>
				</tr>
			';
			foreach($dosen_schedule_data as $dosen_schedule_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-right">Lecturer Name</th>
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
				
				';
			}

			$html .= '
				<tr>
					<th width="40%" class="text-right">Appointment Time</th>
					<td>'.$appointment_row["appointment_time"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Reason for Appointment</th>
					<td>'.$appointment_row["reason_for_appointment"].'</td>
				</tr>
			';

			if($appointment_row["status"] != 'Cancel')
			{

				if($_SESSION['type'] == 'Dosen')
				{

					if($appointment_row["status"] == 'Booked')
					{
						$html .= '
						<tr>
							<th width="40%" class="text-right">Appointment Approval</th>
							<td>
								<select name="appointment_approval" id="appointment_approval" class="form-control" required>
									<option value="Yes" selected>Yes</option>
								</select>
							</td>
						</tr>
						';
					}

					if($appointment_row['appointment_approval'] == 'Yes')
					{
						
						if($appointment_row["status"] == 'Completed')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Appointment Approval</th>
									<td>Yes</td>
								</tr>
									<th width="40%" class="text-right">Student come into Institut</th>
									<td>'.$appointment_row["student_come_into_institut"].'</td>
								<tr>
									<th width="40%" class="text-right">Lecturer Comment</th>
									<td>'.$appointment_row["dosen_comment"].'</td>
								</tr>
							';
						}
						else if($appointment_row["status"] == 'In Process')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Appointment Approval</th>
									<td>Yes</td>
								</tr>
								<tr>
									<th width="40%" class="text-right">Student come into Institut</th>
									<td>
										<select name="student_come_into_institut" id="student_come_into_institut" class="form-control" required>
											<option value="">Select</option>
											<option value="Yes" selected>Yes</option>
											<option value="No" selected>No</option>
										</select>
									</td>
								</tr>
								<tr>
									<th width="40%" class="text-right">Lecturer Comment</th>
									<td>
										<textarea name="dosen_comment" id="dosen_comment" class="form-control" rows="8">'.$appointment_row["dosen_comment"].'</textarea>
									</td>
								</tr>
							';
						}
					}	
					
				}		
			}

			$html .= '
			</table>
			';
		}

		echo $html;
	}

	if($_POST['action'] == 'change_appointment_status')
	{
		if($_SESSION['type'] == 'Dosen')
		{
			$data = array(
				':status'							=>	'In Process',
				':appointment_approval'				=>	'Yes',
				':appointment_id'					=>	$_POST['hidden_appointment_id']
			);

			$object->query = "
			UPDATE appointment_table 
			SET status = :status, 
			appointment_approval = :appointment_approval 
			WHERE appointment_id = :appointment_id
			";

			$object->execute($data);

			echo '<div class="alert alert-success">Appointment Status change to In Process</div>';
		}

		if($_SESSION['type'] == 'Dosen')
		{
			if(isset($_POST['student_come_into_institut']))
			{
				$data = array(
					':status'							=>	'Completed',
					':student_come_into_institut'		=>	$_POST['student_come_into_institut'],
					':dosen_comment'					=>	$_POST['dosen_comment'],
					':appointment_id'					=>	$_POST['hidden_appointment_id']
				);

				$object->query = "
				UPDATE appointment_table 
				SET status = :status,
				student_come_into_institut = :student_come_into_institut,
				dosen_comment = :dosen_comment 
				WHERE appointment_id = :appointment_id
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Appointment Completed</div>';
			}
		}
	}
	

	if($_POST['action'] == 'cancel')
	{
		$data = array(
			':status'							=>	'Cancel',
			':appointment_approval'				=>	'No',
			':appointment_id'					=>	$_POST['appointment_id']
			
		);
		$object->query = "
		UPDATE appointment_table 
		SET status = :status,
		appointment_approval = :appointment_approval 
		WHERE appointment_id = :appointment_id
		";
		$object->execute($data);
		echo '<div class="alert alert-success">Your Appointment has been Cancel</div>';
	}
	
}

?>