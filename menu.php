<nav class="navbar navbar-expand-lg navbar-dark">
	<!-- Toggler Button -->
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	
	<!-- Navbar links -->
	<div class="collapse navbar-collapse" id="collapsibleNavbar">
		<ul class="navbar-nav ml-auto">
			
			<!-- Home -->
			<li class="nav-item">
				<a class="nav-link" href="index.php">
					<i class="fa fa-home"></i> Home
				</a>
			</li>
			
			<!-- View Dropdown 
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="viewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-eye"></i> View
				</a>
				<div class="dropdown-menu" aria-labelledby="viewDropdown">
					<a class="dropdown-item" href="hod_list.php">
						<i class="fa fa-list"></i> Head List
					</a>
					<a class="dropdown-item" href="staff_list.php">
						<i class="fa fa-users"></i> Staff List
					</a>
					<a class="dropdown-item" href="student_list.php">
						<i class="fa fa-graduation-cap"></i> Student List
					</a>
				</div>
			</li>-->
			
			<!-- Add Dropdown 
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="addDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-plus-circle"></i> Add
				</a>
				<div class="dropdown-menu" aria-labelledby="addDropdown">
					<a class="dropdown-item" href="addstaff.php">
						<i class="fa fa-user-plus"></i> Add New Staff
					</a>
					<a class="dropdown-item" href="addstudent.php">
						<i class="fa fa-user"></i> Add New Student
					</a>
				</div>
			</li>-->
			
			<!-- Edit Dropdown 
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="editDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-edit"></i> Edit
				</a>
				<div class="dropdown-menu" aria-labelledby="editDropdown">
					<a class="dropdown-item" href="editstaff.php">
						<i class="fa fa-pencil"></i> Edit Staff
					</a>
					<a class="dropdown-item" href="editstudent.php">
						<i class="fa fa-pencil-square"></i> Edit Student
					</a>
				</div>
			</li>-->
			
			<!-- Meetings Dropdown 
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="meetingsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-calendar"></i> Meetings
				</a>
				<div class="dropdown-menu" aria-labelledby="meetingsDropdown">
					<a class="dropdown-item" href="addmeeting.php">
						<i class="fa fa-calendar-plus-o"></i> Schedule Meeting
					</a>
					<a class="dropdown-item" href="meetings_list.php">
						<i class="fa fa-calendar-check-o"></i> View Meetings
					</a>
				</div>
			</li>-->
			
			<!-- Register User 
			<li class="nav-item">
				<a class="nav-link" href="account/register.php">
					<i class="fa fa-user-plus"></i> Register User
				</a>
			</li>-->
			
			<!-- Reset Password -->
			<?php if (isset($_SESSION["cat"]) && $_SESSION["cat"] != '1') { ?>
			<li class="nav-item">
				<a class="nav-link" href="staff-reset-password.php">
					<i class="fa fa-key"></i> Reset Password
				</a>
			</li>
			<?php } ?>

			<!-- Logout -->
			<li class="nav-item">
				<a class="nav-link" href="account/logout.php" onclick="return confirm('Are you sure you want to logout?');">
					<i class="fa fa-sign-out"></i> Logout
				</a>
			</li>
		</ul>
	</div>
</nav>
