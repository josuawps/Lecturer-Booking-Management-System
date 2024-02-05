<?php

//dosen.php

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
                    <h1 class="h3 mb-4 text-gray-800">Lecturer Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Lecturer List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_dosen" id="add_dosen" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dosen_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Email Address</th>
                                            <th>Password</th>
                                            <th>Lecturer Name</th>
                                            <th>Lecturer Phone No.</th>
                                            <!-- <th>Lecturer Speciality</th> -->
                                            <th>Status</th>
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

<div id="dosenModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="dosen_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Lecturer</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Lecturer Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_email_address" id="dosen_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Lecturer Password <span class="text-danger">*</span></label>
                                <input type="password" name="dosen_password" id="dosen_password" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Lecturer Name <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_name" id="dosen_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Lecturer Phone No. <span class="text-danger">*</span></label>
                                <input type="text" name="dosen_phone_no" id="dosen_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Lecturer Image <span class="text-danger">*</span></label>
                        <br />
                        <input type="file" name="dosen_profile_image" id="dosen_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_dosen_profile_image" id="hidden_dosen_profile_image" />
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Lecturer Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="dosen_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#dosen_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"dosen_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2, 3, 4, 5],
				"orderable":false,
			},
		],
	});


	$('#add_dosen').click(function(){
		
		$('#dosen_form')[0].reset();

		$('#dosen_form').parsley().reset();

    	$('#modal_title').text('Add Lecturer');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#dosenModal').modal('show');

    	$('#form_message').html('');

	});

	$('#dosen_form').parsley();

	$('#dosen_form').on('submit', function(event){
		event.preventDefault();
		if($('#dosen_form').parsley().isValid())
		{		
			$.ajax({
				url:"dosen_action.php",
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
						$('#dosenModal').modal('hide');
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


	$(document).on('click', '.status_button', function(){
		var id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'Active';
		if(status == 'Active')
		{
			next_status = 'Inactive';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{

      		$.ajax({

        		url:"dosen_action.php",

        		method:"POST",

        		data:{id:id, action:'change_status', status:status, next_status:next_status},

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

    $(document).on('click', '.view_button', function(){
        var dosen_id = $(this).data('id');

        $.ajax({

            url:"dosen_action.php",

            method:"POST",

            data:{dosen_id:dosen_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.dosen_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Lecturer Email Address</th><td width="60%">'+data.dosen_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Lecturer Password</th><td width="60%">'+data.dosen_password+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Lecturer Name</th><td width="60%">'+data.dosen_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Lecturer Phone No.</th><td width="60%">'+data.dosen_phone_no+'</td></tr>';


                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#dosen_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"dosen_action.php",

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