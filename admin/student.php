<?php

//student.php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Student Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Student List</h6>
                            	</div>
                            	<div class="col" align="right">
								<!-- <button type="button" name="add_student" id="add_student" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button> -->
                         
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="student_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Contact No.</th>
                                            <th>Email Verification Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>
   

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Student Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="student_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#student_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"student_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[5],
				"orderable":false,
			},
		],
	});

    $('#student_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

    $('#add_student').click(function(){
		
		$('#student_form')[0].reset();

		$('#student_form').parsley().reset();

    	$('#modal_title').text('Add Student');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#studentModal').modal('show');

    	$('#form_message').html('');

	});

    $('#student_form').parsley();

	$('#student_form').on('submit', function(event){
		event.preventDefault();
		if($('#student_form').parsley().isValid())
		{		
			$.ajax({
				url:"student_action.php",
				method:"POST",
				data: new FormData(this),
				dataType:'json',
                contentType: false,
                cache: false,
                processData:false,
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#studentModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

    $(document).on('click', '.edit_button', function(){

var dosen_id = $(this).data('id');

$('#student_form').parsley().reset();

$('#form_message').html('');

$.ajax({

      url:"student_action.php",

      method:"POST",

      data:{student_id:student_id, action:'fetch_single'},

      dataType:'JSON',

      success:function(data)
      {

        $('#student_email_address').val(data.student_email_address);

        $('#student_email_address').val(data.student_email_address);
        $('#student_password').val(data.student_password);
        $('#student_name').val(data.student_name);
        $('#uploaded_image').html('<img src="'+data.student_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
        $('#hidden_student_profile_image').val(data.student_profile_image);
        $('#student_phone_no').val(data.student_phone_no);
        $('#student_address').val(data.student_address);
        $('#student_date_of_birth').val(data.student_date_of_birth);
        $('#student_degree').val(data.student_degree);
        $('#student_expert_in').val(data.student_expert_in);

        $('#modal_title').text('Edit Lecturer');

        $('#action').val('Edit');

        $('#submit_button').val('Edit');

        $('#dosenModal').modal('show');

        $('#hidden_id').val(dosen_id);

      }

})

});

    $(document).on('click', '.view_button', function(){

        var student_id = $(this).data('id');

        $.ajax({

            url:"student_action.php",

            method:"POST",

            data:{student_id:student_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><th width="40%" class="text-right">Email Address</th><td width="60%">'+data.student_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Password</th><td width="60%">'+data.student_password+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Student Name</th><td width="60%">'+data.student_first_name+' '+data.student_last_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Contact No.</th><td width="60%">'+data.student_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Address</th><td width="60%">'+data.student_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Date of Birth</th><td width="60%">'+data.student_date_of_birth+'</td></tr>';
                html += '<tr><th width="40%" class="text-right">Gender</th><td width="60%">'+data.student_gender+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Student Class Year</th><td width="60%">'+data.student_class_year+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Email Verification Status</th><td width="60%">'+data.email_verify+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#student_details').html(html);

            }

        })
    });
    $('#add_student').click(function(){
		
		$('#student_form')[0].reset();

		$('#student_form').parsley().reset();

    	$('#modal_title').text('Add Student');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#studentModal').modal('show');

    	$('#form_message').html('');

	});

	$('#student_form').parsley();

	$('#student_form').on('submit', function(event){
		event.preventDefault();
		if($('#student_form').parsley().isValid())
		{		
			$.ajax({
				url:"student_action.php",
				method:"POST",
				data: new FormData(this),
				dataType:'json',
                contentType: false,
                cache: false,
                processData:false,
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#studentModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"student_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}

  	});



});
</script>