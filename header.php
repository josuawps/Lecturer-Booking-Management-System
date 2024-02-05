<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Lecturer Schedule Booking Management System</title>

	    <!-- Custom styles for this page -->
	    <link href="vendor/bootstrap/bootstrap.min.css" rel="stylesheet">

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

	    <link rel="stylesheet" type="text/css" href="vendor/parsley/parsley.css"/>

	    <link rel="stylesheet" type="text/css" href="vendor/datepicker/bootstrap-datepicker.css"/>

	    <!-- Custom styles for this page -->
    	<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
	    <style>
	    	.border-top { border-top: 1px solid #e5e5e5; }
			.border-bottom { border-bottom: 1px solid #e5e5e5; }

			.box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }

		.gambar{
			
			width:787px;
			height: 276px;
			align-items: center;
			justify-content: center;
			margin-left: 550px;
		}
	    </style>
	</head>
	<body>

		<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
			<div class="col">
		    	<h5 class="my-0 mr-md-auto font-weight-normal">PA-I Kel.4</h5>
		    </div>
		    <?php
		    if(!isset($_SESSION['student_id']))
		    {
		    ?>
		    <div class="col text-right"><a href="login.php">Login</a></div>
		   	<?php
		   	}
		   	?>
	    </div>  
	    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
		<div class="card mb-2">
  <img src="./images/logo booking.jpg" class="gambar" alt="...">
  </div>
	      	<h1 class="display-4"> Lecturer Schedule Booking Management System</h1>
	    
		</div>
	    <br />
	    <br />
	    <div class="container-fluid">