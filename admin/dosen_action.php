<?php

//dosen_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('dosen_name', 'dosen_status');

		$output = array();

		$main_query = "
		SELECT * FROM dosen_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE dosen_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			// $search_query .= 'OR dosen_date_of_birth LIKE "%'.$_POST["search"]["value"].'%" ';
			// $search_query .= 'OR dosen_degree LIKE "%'.$_POST["search"]["value"].'%" ';
			// $search_query .= 'OR dosen_expert_in LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR dosen_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY dosen_id DESC ';
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
			$sub_array[] = '<img src="'.$row["dosen_profile_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["dosen_email_address"];
			$sub_array[] = $row["dosen_password"];
			$sub_array[] = $row["dosen_name"];
			$sub_array[] = $row["dosen_phone_no"];
			// $sub_array[] = $row["dosen_expert_in"];
			$status = '';
			if($row["dosen_status"] == 'Active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["dosen_id"].'" data-status="'.$row["dosen_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["dosen_id"].'" data-status="'.$row["dosen_status"].'">Inactive</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["dosen_id"].'"><i class="fas fa-eye"></i></button>
			
			
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["dosen_id"].'"><i class="fas fa-times"></i></button>
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

		$data = array(
			':dosen_email_address'	=>	$_POST["dosen_email_address"]
		);

		$object->query = "
		SELECT * FROM dosen_table 
		WHERE dosen_email_address = :dosen_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$dosen_profile_image = '';
			if($_FILES['dosen_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["dosen_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["dosen_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['dosen_profile_image']['tmp_name'], $destination);

					$dosen_profile_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["dosen_name"][0];
				$path = "../images/". time() . ".png";
				$image = imagecreate(200, 200);
				$red = rand(0, 255);
				$green = rand(0, 255);
				$blue = rand(0, 255);
			    imagecolorallocate($image, 230, 230, 230);  
			    $textcolor = imagecolorallocate($image, $red, $green, $blue);
			    imagettftext($image, 100, 0, 55, 150, $textcolor, '../font/arial.ttf', $character);
			    imagepng($image, $path);
			    imagedestroy($image);
			    $dosen_profile_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':dosen_email_address'			=>	$object->clean_input($_POST["dosen_email_address"]),
					':dosen_password'				=>	$_POST["dosen_password"],
					':dosen_name'					=>	$object->clean_input($_POST["dosen_name"]),
					':dosen_profile_image'			=>	$dosen_profile_image,
					 ':dosen_phone_no'				=>	$object->clean_input($_POST["dosen_phone_no"]),
					':dosen_status'				=>	'Active',
					':dosen_added_on'				=>	$object->now
				);

				$object->query = "
				INSERT INTO dosen_table 
				(dosen_email_address, dosen_password, dosen_name, dosen_profile_image, dosen_phone_no, dosen_status, dosen_added_on) 
				VALUES (:dosen_email_address, :dosen_password, :dosen_name, :dosen_profile_image, :dosen_phone_no, :dosen_status, :dosen_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Lecturer Added</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM dosen_table 
		WHERE dosen_id = '".$_POST["dosen_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['dosen_email_address'] = $row['dosen_email_address'];
			$data['dosen_password'] = $row['dosen_password'];
			$data['dosen_name'] = $row['dosen_name'];
			$data['dosen_profile_image'] = $row['dosen_profile_image'];
			$data['dosen_phone_no'] = $row['dosen_phone_no'];
		}

		echo json_encode($data);
	}

	

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':dosen_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE dosen_table 
		SET dosen_status = :dosen_status 
		WHERE dosen_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM dosen_table 
		WHERE dosen_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Lecturer Data Deleted</div>';
	}
}

?>