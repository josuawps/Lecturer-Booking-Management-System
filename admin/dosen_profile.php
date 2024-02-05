<?php
//dosen_profile.php
include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if($_SESSION['type'] != 'Dosen')
{
    header("location:".$object->base_url."");
}

$object->query = "
    SELECT * FROM dosen_table
    WHERE dosen_id = '".$_SESSION["admin_id"]."'
    ";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-10"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="dosen_profile" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                               
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
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

   
    <?php
    foreach($result as $row)
    {
    ?>
    $('#hidden_id').val("<?php echo $row['dosen_id']; ?>");
    $('#dosen_email_address').val("<?php echo $row['dosen_email_address']; ?>");
    $('#dosen_password').val("<?php echo $row['dosen_password']; ?>");
    $('#dosen_name').val("<?php echo $row['dosen_name']; ?>");
    $('#dosen_phone_no').val("<?php echo $row['dosen_phone_no']; ?>");

    
    $('#uploaded_image').html('<img src="<?php echo $row["dosen_profile_image"]; ?>" class="img-thumbnail" width="100" /><input type="hidden" name="hidden_dosen_profile_image" value="<?php echo $row["dosen_profile_image"]; ?>" />');

    $('#hidden_dosen_profile_image').val("<?php echo $row['dosen_profile_image']; ?>");
    <?php
    }
    ?>

    $('#dosen_profile_image').change(function(){
        var extension = $('#dosen_profile_image').val().split('.').pop().toLowerCase();
        if(extension != '')
        {
            if(jQuery.inArray(extension, ['png','jpg']) == -1)
            {
                alert("Invalid Image File");
                $('#dosen_profile_image').val('');
                return false;
            }
        }
    });

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"profile_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                    $('#dosen_email_address').val(data.dosen_email_address);
                    $('#dosen_password').val(data.dosen_password);
                    $('#dosen_name').val(data.dosen_name);
                    $('#dosen_phone_no').val(data.dosen_phone_no);
          
                    if(data.dosen_profile_image != '')
                    {
                        $('#uploaded_image').html('<img src="'+data.dosen_profile_image+'" class="img-thumbnail" width="100" />');

                        $('#user_profile_image').attr('src', data.dosen_profile_image);
                    }

                    $('#hidden_dosen_profile_image').val(data.dosen_profile_image);
						
                    $('#message').html(data.success);

					setTimeout(function(){

				        $('#message').html('');

				    }, 5000);
				}
			})
		}
	});

});
</script>