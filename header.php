<head>
	<?php
		include ("connection/connect.php");
		date_default_timezone_set("Asia/Colombo");
	?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/custom.css">	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="icon" type="image/png" href="images/icons/logo.png"/>
	
	 
    <!-- JavaScript -->    
    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-validate.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	
    <title>Hostel Management System</title>
</head>
<body>
	<div class="main-header">
		<div class="row align-items-center">
			<!-- Logo Section -->
			<div class="col-lg-2 col-md-3 col-sm-12 mb-3 mb-md-0">
				<div class="logo-container">
					<a href="index.php">
						<img class="img-fluid" src="images/logoM.png" alt="Hostel Management System Logo" />
					</a>
				</div>
			</div>
			
			<!-- Menu Section -->
			<div class="col-lg-7 col-md-6 col-sm-12 mb-3 mb-md-0">
				<?php include 'menu.php'; ?>
			</div>
			
			<!-- Welcome Section -->
			<div class="col-lg-3 col-md-3 col-sm-12">
				<div class="welcome-section">
					<div class="welcome-text">
						<i class="fa fa-user-circle"></i> Welcome <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>
					</div>
				</div>
			</div>
		</div>	
	</div>