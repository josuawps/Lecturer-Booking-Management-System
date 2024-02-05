<?php

//index.php

include('class/Appointment.php');

$object = new Appointment;

if(isset($_SESSION['student_id']))
{
	header('location:dashboard.php');
}

$object->query = "
SELECT * FROM dosen_schedule_table 
INNER JOIN dosen_table 
ON dosen_table.dosen_id = dosen_schedule_table.dosen_id
WHERE dosen_schedule_table.dosen_schedule_date >= '".date('Y-m-d')."' 
AND dosen_schedule_table.dosen_schedule_status = 'Active' 
AND dosen_table.dosen_status = 'Active' 
ORDER BY dosen_schedule_table.dosen_schedule_date ASC
";

$result = $object->get_result();

include('header.php');

?>
		      	<div class="card">
		      		<form method="post" action="result.php">
			      		<div class="card-header"><h3><b>Lecturer Schedule List</b></h3></div>
			      		<div class="card-body">
		      				<div class="table-responsive">
		      					<table class="table table-striped table-bordered">
		      						<tr>
		      							<th>Lecturer Name</th>
		      							<th>Appointment Date</th>
		      							<th>Appointment Day</th>
		      							<th>Available Time</th>
		      							<th>Action</th>
		      						</tr>
		      						<?php
		      						foreach($result as $row)
		      						{
		      							echo '
		      							<tr>
		      								<td>'.$row["dosen_name"].'</td>
		      								<td>'.$row["dosen_schedule_date"].'</td>
		      								<td>'.$row["dosen_schedule_day"].'</td>
		      								<td>'.$row["dosen_schedule_start_time"].' - '.$row["dosen_schedule_end_time"].'</td>
		      								<td><button type="button" name="get_appointment" class="btn btn-primary btn-sm get_appointment" data-id="'.$row["dosen_schedule_id"].'">Get Appointment</button></td>
		      							</tr>
		      							';
		      						}
		      						?>
		      					</table>
		      				</div>
		      			</div>
		      		</form>
		      	</div>
		    

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){
	$(document).on('click', '.get_appointment', function(){
		var action = 'check_login';
		var dosen_schedule_id = $(this).data('id');
		$.ajax({
			url:"action.php",
			method:"POST",
			data:{action:action, dosen_schedule_id:dosen_schedule_id},
			success:function(data)
			{
				window.location.href=data;
			}
		})
	});
});

</script>