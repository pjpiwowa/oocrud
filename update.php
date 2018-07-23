<?php
	require 'config.php';
	require 'table.php';
	require 'util.php';

	$id = null;
	if (!empty($_GET['id']))
	{
		$id = $_REQUEST['id'];
	} else
	{
		header("Location: index.php");
	}

	if (!empty($_POST))
	{
		// Validation error messages, left null if validation succeeds
		$nameError = null;
		$emailError = null;
		$mobileError = null;

		$name = $_POST['name'];
		$email = $_POST['email'];
		$mobile = $_POST['mobile'];

		$valid = true;
		if (empty($name))
		{
			$nameError = 'Please enter Name';
			$valid = false;
		}
		
		if (empty($email))
		{
			$emailError = 'Please enter Email Address';
			$valid = false;
		} else if (!filter_var($email,FILTER_VALIDATE_EMAIL))
		{
			$emailError = 'Please enter a valid Email Address';
			$valid = false;
		}
		
		if (empty($mobile))
		{
			$mobileError = 'Please enter Mobile Number';
			$valid = false;
		}
		
		// update data
		if ($valid)
		{
			$db = new Database($DB_NAME, $DB_HOST, $DB_USER, $DB_PASS);
			$tbl = new cruddy_table($db, "customers", array("name" => "Name", "email" => "Email Address", "mobile" => "Phone Number"));

			$tbl->mod_row($id, array("name" => $name, "email" => $email, "mobile" => $mobile));

			header("Location: index.php");
		}
	}
	else
	{
		// Populate the form with current contents.

		$db = new Database($DB_NAME, $DB_HOST, $DB_USER, $DB_PASS);
		$tbl = new cruddy_table($db, "customers", array("name" => "Name", "email" => "Email Address", "mobile" => "Phone Number"));

		$data = $tbl->get_row($id);

		$name = $data['name'];
		$email = $data['email'];
		$mobile = $data['mobile'];
	}
?>

<!DOCTYPE html>
<html lang="en">

<?php echo cruddy_head(); ?>
 
<body>
<div class="container">
	<div class="span10 offset1">
		<div class="row">
			<h3>Update a Customer</h3>
		</div>

		<form class="form-horizontal" action="update.php?id=<?php echo $id?>" method="post">
		<div class="control-group <?php echo !empty($nameError)?'error':'';?>">
			<label class="control-label">Name</label>
			<div class="controls">
				<input name="name" type="text"  placeholder="Name" value="<?php echo !empty($name)?$name:'';?>">
				<?php if (!empty($nameError)): ?>
					<span class="help-inline"><?php echo $nameError;?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="control-group <?php echo !empty($emailError)?'error':'';?>">
			<label class="control-label">Email Address</label>
			<div class="controls">
				<input name="email" type="text" placeholder="Email Address" value="<?php echo !empty($email)?$email:'';?>">
				<?php if (!empty($emailError)): ?>
					<span class="help-inline"><?php echo $emailError;?></span>
				<?php endif;?>
			</div>
		</div>
		<div class="control-group <?php echo !empty($mobileError)?'error':'';?>">
			<label class="control-label">Mobile Number</label>
			<div class="controls">
				<input name="mobile" type="text"  placeholder="Mobile Number" value="<?php echo !empty($mobile)?$mobile:'';?>">
				<?php if (!empty($mobileError)): ?>
					<span class="help-inline"><?php echo $mobileError;?></span>
				<?php endif;?>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-success">Update</button>
			<a class="btn" href="index.php">Back</a>
			</div>
		</form>
	</div>
</div> <!-- /container -->
</body>
</html>
