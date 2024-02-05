<?php

include('../class/Appointment.php');

$object = new Appointment;

if($_POST["action"] == 'dosen_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$dosen_profile_image = '';

	$data = array(
		':dosen_email_address'	=>	$_POST["dosen_email_address"],
		':dosen_id'			=>	$_POST['hidden_id']
	);

	$object->query = "
	SELECT * FROM dosen_table 
	WHERE dosen_email_address = :dosen_email_address 
	AND dosen_id != :dosen_id
	";

	$object->execute($data);

	if($object->row_count() > 0)
	{
		$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
	}
	else
	{
		$dosen_profile_image = $_POST["hidden_dosen_profile_image"];

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

		if($error == '')
		{
			$data = array(
				':dosen_email_address'			=>	$object->clean_input($_POST["dosen_email_address"]),
				':dosen_password'				=>	$_POST["dosen_password"],
				':dosen_name'					=>	$object->clean_input($_POST["dosen_name"]),
				':dosen_profile_image'			=>	$dosen_profile_image,
				':dosen_phone_no'				=>	$object->clean_input($_POST["dosen_phone_no"]),
		
			);

			$object->query = "
			UPDATE dosen_table  
			SET dosen_email_address = :dosen_email_address, 
			dosen_password = :dosen_password, 
			dosen_name = :dosen_name, 
			dosen_profile_image = :dosen_profile_image, 
			dosen_phone_no = :dosen_phone_no, 
			WHERE dosen_id = '".$_POST['hidden_id']."'
			";
			$object->execute($data);

			$success = '<div class="alert alert-success">dosen Data Updated</div>';
		}			
	}

	$output = array(
		'error'					=>	$error,
		'success'				=>	$success,
		'dosen_email_address'	=>	$_POST["dosen_email_address"],
		'dosen_password'		=>	$_POST["dosen_password"],
		'dosen_name'			=>	$_POST["dosen_name"],
		'dosen_profile_image'	=>	$dosen_profile_image,
		'dosen_phone_no'		=>	$_POST["dosen_phone_no"],
	
	);

	echo json_encode($output);
}

if($_POST["action"] == 'admin_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$institut_logo = $_POST['hidden_institut_logo'];

	if($_FILES['institut_logo']['name'] != '')
	{
		$allowed_file_format = array("jpg", "png");

	    $file_extension = pathinfo($_FILES["institut_logo"]["name"], PATHINFO_EXTENSION);

	    if(!in_array($file_extension, $allowed_file_format))
		{
		    $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
		}
		else if (($_FILES["institut_logo"]["size"] > 2000000))
		{
		   $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
	    }
		else
		{
		    $new_name = rand() . '.' . $file_extension;

			$destination = '../images/' . $new_name;

			move_uploaded_file($_FILES['institut_logo']['tmp_name'], $destination);

			$institut_logo = $destination;
		}
	}

	if($error == '')
	{
		$data = array(
			':admin_email_address'			=>	$object->clean_input($_POST["admin_email_address"]),
			':admin_password'				=>	$_POST["admin_password"],
			':admin_name'					=>	$object->clean_input($_POST["admin_name"]),
			':institut_name'				=>	$object->clean_input($_POST["institut_name"]),
			':institut_address'				=>	$object->clean_input($_POST["institut_address"]),
			':institut_contact_no'			=>	$object->clean_input($_POST["institut_contact_no"]),
			':institut_logo'				=>	$institut_logo
		);

		$object->query = "
		UPDATE admin_table  
		SET admin_email_address = :admin_email_address, 
		admin_password = :admin_password, 
		admin_name = :admin_name, 
		institut_name = :institut_name, 
		institut_address = :institut_address, 
		institut_contact_no = :institut_contact_no, 
		institut_logo = :institut_logo 
		WHERE admin_id = '".$_SESSION["admin_id"]."'
		";
		$object->execute($data);

		$success = '<div class="alert alert-success">Admin Data Updated</div>';

		$output = array(
			'error'					=>	$error,
			'success'				=>	$success,
			'admin_email_address'	=>	$_POST["admin_email_address"],
			'admin_password'		=>	$_POST["admin_password"],
			'admin_name'			=>	$_POST["admin_name"], 
			'institut_name'			=>	$_POST["institut_name"],
			'institut_address'		=>	$_POST["institut_address"],
			'institut_contact_no'	=>	$_POST["institut_contact_no"],
			'institut_logo'			=>	$institut_logo
		);

		echo json_encode($output);
	}
	else
	{
		$output = array(
			'error'					=>	$error,
			'success'				=>	$success
		);
		echo json_encode($output);
	}
}

?>