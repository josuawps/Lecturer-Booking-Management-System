<?php

//dosen_schedule_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Dosen')
		{
			$order_column = array('dosen_table.dosen_name', 'dosen_schedule_table.dosen_schedule_date', 'dosen_schedule_table.dosen_schedule_day', 'dosen_schedule_table.dosen_schedule_start_time', 'dosen_schedule_table.dosen_schedule_end_time', 'dosen_schedule_table.average_consulting_time', 'dosen_schedule_table.dosen_schedule_room');
			$main_query = "
			SELECT * FROM dosen_schedule_table 
			INNER JOIN dosen_table 
			ON dosen_table.dosen_id = dosen_schedule_table.dosen_id 
			";

			$search_query = '';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'WHERE dosen_table.dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_end_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.average_consulting_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_table.dosen_schedule_room LIKE "%'.$_POST["search"]["value"].'%" ';
			}
		}
		else
		{
			$order_column = array('dosen_schedule_date', 'dosen_schedule_day', 'dosen_schedule_start_time', 'dosen_schedule_end_time', 'average_consulting_time', 'dosen_schedule_room');
			$main_query = "
			SELECT * FROM dosen_schedule_table 
			";

			$search_query = '
			WHERE dosen_id = "'.$_SESSION["admin_id"].'" AND 
			';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= '(dosen_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR dosen_schedule_end_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR average_consulting_time LIKE "%'.$_POST["search"]["value"].'%") ';
				$search_query .= 'OR dosen_schedule_room LIKE "%'.$_POST["search"]["value"].'%") ';
			}
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY dosen_schedule_table.dosen_schedule_id DESC ';
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

		$object->query = $main_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = html_entity_decode($row["dosen_name"]);
			}
			$sub_array[] = $row["dosen_schedule_date"];

			$sub_array[] = $row["dosen_schedule_day"];

			$sub_array[] = $row["dosen_schedule_start_time"];

			$sub_array[] = $row["dosen_schedule_end_time"];

			$sub_array[] = $row["average_consulting_time"] . ' Minute';

			$sub_array[] = $row["dosen_schedule_room"];

			$status = '';
			if($row["dosen_schedule_status"] == 'Active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["dosen_schedule_id"].'" data-status="'.$row["dosen_schedule_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["dosen_schedule_id"].'" data-status="'.$row["dosen_schedule_status"].'">Inactive</button>';
			}

			$sub_array[] = $status;

			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["dosen_schedule_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["dosen_schedule_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$dosen_id = '';

		if($_SESSION['type'] == 'Admin')
		{
			$dosen_id = $_POST["dosen_id"];
		}

		if($_SESSION['type'] == 'Dosen')
		{
			$dosen_id = $_SESSION['admin_id'];
		}

		$data = array(
			':dosen_id'					    =>	$dosen_id,
			':dosen_schedule_date'			=>	$_POST["dosen_schedule_date"],
			':dosen_schedule_day'			=>	date('l', strtotime($_POST["dosen_schedule_date"])),
			':dosen_schedule_start_time'	=>	$_POST["dosen_schedule_start_time"],
			':dosen_schedule_end_time'		=>	$_POST["dosen_schedule_end_time"],
			':average_consulting_time'		=>	$_POST["average_consulting_time"],
			':dosen_schedule_room'		    =>	$_POST["dosen_schedule_room"]


		);

		$object->query = "
		INSERT INTO dosen_schedule_table 
		(dosen_id, dosen_schedule_date, dosen_schedule_day, dosen_schedule_start_time, dosen_schedule_end_time, average_consulting_time, dosen_schedule_room) 
		VALUES (:dosen_id, :dosen_schedule_date, :dosen_schedule_day, :dosen_schedule_start_time, :dosen_schedule_end_time, :average_consulting_time, :dosen_schedule_room)
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Dosen Schedule Added Successfully</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM dosen_schedule_table 
		WHERE dosen_schedule_id = '".$_POST["dosen_schedule_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['dosen_id'] = $row['dosen_id'];
			$data['dosen_schedule_date'] = $row['dosen_schedule_date'];
			$data['dosen_schedule_start_time'] = $row['dosen_schedule_start_time'];
			$data['dosen_schedule_end_time'] = $row['dosen_schedule_end_time'];
			$data['average_consulting_time'] = $row['average_consulting_time'];
			$data['dosen_schedule_room'] = $row['dosen_schedule_room'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$dosen_id = '';

		if($_SESSION['type'] == 'Admin')
		{
			$dosen_id = $_POST["dosen_id"];
		}

		if($_SESSION['type'] == 'Dosen')
		{
			$dosen_id = $_SESSION['admin_id'];
		}

		$data = array(
			':dosen_id'					    =>	$dosen_id,
			':dosen_schedule_date'			=>	$_POST["dosen_schedule_date"],
			':dosen_schedule_start_time'	=>	$_POST["dosen_schedule_start_time"],
			':dosen_schedule_end_time'		=>	$_POST["dosen_schedule_end_time"],
			':average_consulting_time'		=>	$_POST["average_consulting_time"],
			':dosen_schedule_room'		    =>	$_POST["dosen_schedule_room"]
		);

		$object->query = "
		UPDATE dosen_schedule_table 
		SET dosen_id = :dosen_id, 
		dosen_schedule_date = :dosen_schedule_date, 
		dosen_schedule_start_time = :dosen_schedule_start_time, 
		dosen_schedule_end_time = :dosen_schedule_end_time, 
		average_consulting_time = :average_consulting_time,
		dosen_schedule_room = :dosen_schedule_room    
		WHERE dosen_schedule_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">dosen Schedule Data Updated Successfully Updated</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':dosen_schedule_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE dosen_schedule_table 
		SET dosen_schedule_status = :dosen_schedule_status 
		WHERE dosen_schedule_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Dosen Schedule Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM dosen_schedule_table 
		WHERE dosen_schedule_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Dosen Schedule has been Deleted</div>';
	}
}

?>