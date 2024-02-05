<?php

//download.php

include('class/Appointment.php');

$object = new Appointment;

require_once('class/pdf.php');

if(isset($_GET["id"]))
{
	$html = '<table border="0" cellpadding="5" cellspacing="5" width="100%">';

	$object->query = "
	SELECT institut_name, institut_address, institut_contact_no, institut_logo 
	FROM admin_table
	";

	$institut_data = $object->get_result();

	foreach($institut_data as $institut_row)
	{
		$html .= '<tr><td align="center">';
		if($institut_row['institut_logo'] != '')
		{
			$html .= '<img src="'.substr($institut_row['institut_logo'], 3).'" /><br />';
		}
		$html .= '<h2 align="center">'.$institut_row['institut_name'].'</h2>
		<p align="center">'.$institut_row['institut_address'].'</p>
		<p align="center"><b>Contact No. - </b>'.$institut_row['institut_contact_no'].'</p></td></tr>
		';
	}

	$html .= "
	<tr><td><hr /></td></tr>
	<tr><td>
	";

	$object->query = "
	SELECT * FROM appointment_table 
	WHERE appointment_id = '".$_GET["id"]."'
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
		
		$html .= '
		<h4 align="center">Student Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">';

		foreach($student_data as $student_row)
		{
			$html .= '<tr><th width="50%" align="right">Student Name</th><td>'.$student_row["student_first_name"].' '.$student_row["student_last_name"].'</td></tr>
			<tr><th width="50%" align="right">Contact No.</th><td>'.$student_row["student_phone_no"].'</td></tr>';
		}

		$html .= '</table><br /><hr />
		<h4 align="center">Appointment Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">
			<tr>
				<th width="50%" align="right">Appointment No.</th>
				<td>'.$appointment_row["appointment_number"].'</td>
			</tr>
		';
		foreach($dosen_schedule_data as $dosen_schedule_row)
		{
			$html .= '
			<tr>
				<th width="50%" align="right">Lecturer Name</th>
				<td>'.$dosen_schedule_row["dosen_name"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Date</th>
				<td>'.$dosen_schedule_row["dosen_schedule_date"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Day</th>
				<td>'.$dosen_schedule_row["dosen_schedule_day"].'</td>
			</tr>
				
			';
		}

		$html .= '
			<tr>
				<th width="50%" align="right">Appointment Time</th>
				<td>'.$appointment_row["appointment_time"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Reason for Appointment</th>
				<td>'.$appointment_row["reason_for_appointment"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Student come into Institut </th>
				<td>'.$appointment_row["student_come_into_institut"].'</td>
			</tr>
			<tr>
			<th width="50%" align="right">Appointment Approval </th>
			<td>'.$appointment_row["appointment_approval"].'</td>
		</tr>
			<tr>
				<th width="50%" align="right">Room</th>
				<td>'.$dosen_schedule_row["dosen_schedule_room"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Dosen Comment</th>
				<td>'.$appointment_row["dosen_comment"].'</td>
			</tr>
		</table>
			';
	}

	$html .= '
			</td>
		</tr>
	</table>';

	echo $html;

	$pdf = new Pdf();

	$pdf->loadHtml($html, 'UTF-8');
	$pdf->render();
	ob_end_clean();
	$pdf->stream($_GET["id"] . '.pdf', array( 'Attachment'=>1 ));
	$pdf->stream($_GET["id"] . '.pdf', array( 'Attachment'=>false ));
	exit(0);

}

?>