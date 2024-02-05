<?php

//Appointment.php

class Appointment
{
	public $base_url = 'http://localhost:8080/Proyek-PA-1/lecturer-booking-management-system/';
	public $connect;
	public $query;
	public $statement;
	public $now;

	public function __construct()
	{
		$this->connect = new PDO("mysql:host=localhost;dbname=dosen_appointment", "root", "");

		date_default_timezone_set('Asia/Jakarta');

		session_start();

		$this->now = date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
	}

	function execute($data = null)
	{
		$this->statement = $this->connect->prepare($this->query);
		if($data)
		{
			$this->statement->execute($data);
		}
		else
		{
			$this->statement->execute(NULL);
		}		
	}

	function row_count()
	{
		return $this->statement->rowCount();
	}

	function statement_result()
	{
		return $this->statement->fetchAll();
	}

	function get_result()
	{
		return $this->connect->query($this->query, PDO::FETCH_ASSOC);
	}

	function is_login()
	{
		if(isset($_SESSION['admin_id']))
		{
			return true;
		}
		return false;
	}

	function clean_input($string)
	{
	  	$string = trim($string);
	  	$string = stripslashes($string);
	  	$string = htmlspecialchars($string);
	  	return $string;
	}

	function Generate_appointment_no()
	{
		$this->query = "
		SELECT MAX(appointment_number) as appointment_number FROM appointment_table 
		";

		$result = $this->get_result();

		$appointment_number = 0;

		foreach($result as $row)
		{
			$appointment_number = $row["appointment_number"];
		}

		if($appointment_number > 0)
		{
			return $appointment_number + 1;
		}
		else
		{
			return '1000';
		}
	}

	function get_total_today_appointment()
	{
		$this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN dosen_schedule_table 
		ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
		WHERE dosen_schedule_date = CURDATE() 
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_yesterday_appointment()
	{
		$this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN dosen_schedule_table 
		ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
		WHERE dosen_schedule_date = CURDATE() - 1
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_seven_day_appointment()
	{
		$this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN dosen_schedule_table 
		ON dosen_schedule_table.dosen_schedule_id = appointment_table.dosen_schedule_id 
		WHERE dosen_schedule_date >= DATE(NOW()) - INTERVAL 7 DAY
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_appointment()
	{
		$this->query = "
		SELECT * FROM appointment_table 
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_student()
	{
		$this->query = "
		SELECT * FROM student_table 
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_lecturer()
	{
		$this->query = "
		SELECT * FROM dosen_table 
		";
		$this->execute();
		return $this->row_count();
	}
}


?>