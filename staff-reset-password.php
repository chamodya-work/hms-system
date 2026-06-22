<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: account/login.php");
	exit;
}

if (!isset($_SESSION["cat"]) || $_SESSION["cat"] == '1' || !isset($_SESSION["id"])) {
	header("location: index.php");
	exit;
}

include("connection/connect.php");

$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";
$success_msg = $error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty(trim($_POST["current_password"]))) {
		$current_password_err = "Please enter your current password.";
	} else {
		$current_password = trim($_POST["current_password"]);
	}

	if (empty(trim($_POST["new_password"]))) {
		$new_password_err = "Please enter the new password.";
	} elseif (strlen(trim($_POST["new_password"])) < 6) {
		$new_password_err = "Password must have atleast 6 characters.";
	} else {
		$new_password = trim($_POST["new_password"]);
	}

	if (empty(trim($_POST["confirm_password"]))) {
		$confirm_password_err = "Please confirm the password.";
	} else {
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($new_password_err) && ($new_password != $confirm_password)) {
			$confirm_password_err = "Password did not match.";
		}
	}

	if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
		$sql = "SELECT password FROM users WHERE id = ? AND username = ?";

		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param("is", $param_id, $param_username);
			$param_id = $_SESSION["id"];
			$param_username = $_SESSION["username"];

			if ($stmt->execute()) {
				$stmt->store_result();

				if ($stmt->num_rows == 1) {
					$stmt->bind_result($hashed_password);

					if ($stmt->fetch()) {
						if (password_verify($current_password, $hashed_password)) {
							$stmt->close();

							$update_sql = "UPDATE users SET password = ? WHERE id = ? AND username = ?";

							if ($update_stmt = $conn->prepare($update_sql)) {
								$param_password = password_hash($new_password, PASSWORD_DEFAULT);
								$update_stmt->bind_param("sis", $param_password, $param_id, $param_username);

								if ($update_stmt->execute()) {
									$success_msg = "Password updated successfully.";
									$current_password = $new_password = $confirm_password = "";
								} else {
									$error_msg = "Oops! Something went wrong. Please try again later.";
								}

								$update_stmt->close();
							} else {
								$error_msg = "Oops! Something went wrong. Please try again later.";
							}
						} else {
							$current_password_err = "Current password is incorrect.";
							$stmt->close();
						}
					}
				} else {
					$error_msg = "Unable to find the logged-in staff account.";
					$stmt->close();
				}
			} else {
				$error_msg = "Oops! Something went wrong. Please try again later.";
				$stmt->close();
			}
		} else {
			$error_msg = "Oops! Something went wrong. Please try again later.";
		}
	}
}
?>
<!doctype html>
<html lang="en">
<?php include 'header.php'; ?>

<div class="container" style="padding:30px;">
	<div style="max-width: 450px; padding: 20px; margin: auto;">
		<h2>Reset Password</h2>

		<?php if (!empty($success_msg)) { ?>
			<div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
		<?php } ?>

		<?php if (!empty($error_msg)) { ?>
			<div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
		<?php } ?>

		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($current_password_err)) ? 'has-error' : ''; ?>">
				<label>Current Password</label>
				<input type="password" name="current_password" class="form-control">
				<span class="help-block text-danger"><?php echo htmlspecialchars($current_password_err); ?></span>
			</div>

			<div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
				<label>New Password</label>
				<input type="password" name="new_password" class="form-control">
				<span class="help-block text-danger"><?php echo htmlspecialchars($new_password_err); ?></span>
			</div>

			<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
				<label>Confirm New Password</label>
				<input type="password" name="confirm_password" class="form-control">
				<span class="help-block text-danger"><?php echo htmlspecialchars($confirm_password_err); ?></span>
			</div>

			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Update Password">
				<a class="btn btn-danger" href="index.php">Cancel</a>
			</div>
		</form>
	</div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
