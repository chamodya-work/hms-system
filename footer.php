<footer class="footer mt-5">
	<div class="container-fluid"
		style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px 0; border-top: 3px solid #0f3460;">
		<div class="row">
			<div class="col-12">
				<p class="text-center mb-2" style="color: #e4e4e4; font-size: 15px; letter-spacing: 0.5px;">
					<i class="fa fa-university" style="color: #4a9eff; margin-right: 8px;"></i>
					<strong>Hostel Management System</strong>
				</p>
				<p class="text-center mb-2" style="color: #b8b8b8; font-size: 14px;">
					Faculty of Medicine, University of Kelaniya, Sri Lanka
				</p>
				<p class="text-center mb-0" style="color: #9a9a9a; font-size: 13px;">
					© <?php echo date("Y"); ?> All Rights Reserved.
				</p>
			</div>
		</div>

		<!-- Optional: Add social links or contact info -->
		<div class="row mt-3">
			<div class="col-12 text-center">
				<p style="color: #66b3ff;">Technical Support:
					<a href="mailto:hostelmed@kln.ac.lk" class="footer-link" style="color: #66b3ff;">
						hostelmed@kln.ac.lk
					</a>
				</p>

			</div>
		</div>
	</div>
</footer>

<style>
	.footer-link:hover {
		color: #66b3ff !important;
		text-decoration: none;
	}

	/* Ensure footer stays at bottom */
	body {
		display: flex;
		flex-direction: column;
		min-height: 100vh;
	}

	.footer {
		margin-top: auto;
	}

	html,
	body {
		height: 100%;
		margin: 0;
	}

	body {
		display: flex;
		flex-direction: column;
	}

	/* This targets your main content container */
	.container {
		flex: 1;
	}

	.footer {
		margin-top: auto;
	}

	.footer-link:hover {
		color: #66b3ff !important;
		text-decoration: none;
	}
</style>