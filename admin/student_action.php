<?php

//student_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('student_first_name', 'student_last_name', 'student_email_address', 'student_phone_no', 'email_verify');

		$output = array();

		$main_query = "
		SELECT * FROM student_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR email_verify LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY student_id DESC ';
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
			$sub_array[] = $row["student_first_name"];
			$sub_array[] = $row["student_last_name"];
			$sub_array[] = $row["student_email_address"];
			$sub_array[] = $row["student_phone_no"];
			$status = '';
			if($row["email_verify"] == 'Yes')
			{
				$status = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$status = '<span class="badge badge-danger">No</span>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["student_id"].'"><i class="fas fa-eye"></i></button>

			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["student_id"].'"><i class="fas fa-times"></i></button>
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
		SELECT * FROM student_table 
		WHERE student_id = '".$_POST["student_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['student_email_address'] = $row['student_email_address'];
			$data['student_password'] = $row['student_password'];
			$data['student_first_name'] = $row['student_first_name'];
			$data['student_last_name'] = $row['student_last_name'];
			$data['student_date_of_birth'] = $row['student_date_of_birth'];
			$data['student_gender'] = $row['student_gender'];
			$data['student_address'] = $row['student_address'];
			$data['student_phone_no'] = $row['student_phone_no'];
			$data['student_class_year'] = $row['student_class_year'];
			if($row['email_verify'] == 'Yes')
			{
				$data['email_verify'] = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$data['email_verify'] = '<span class="badge badge-danger">No</span>';
			}
		}

		echo json_encode($data);
	}
	
	
	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM student_table 
		WHERE student_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Student Data Deleted</div>';
	}
}

?>