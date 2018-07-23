<?php
	require 'config.php';
	require 'table.php';
	require 'util.php';

	$id = 0;

	if ( !empty($_GET['id']))
	{
		$id = $_REQUEST['id'];
	}

	if ( !empty($_POST))
	{
		$id = $_POST['id'];

		$db = new Database($DB_NAME, $DB_HOST, $DB_USER, $DB_PASS);
		$tbl = new cruddy_table($db, "customers", array("name" => "Name", "email" => "Email Address", "mobile" => "Phone Number"));

		$tbl->del_row($id);

		header("Location: index.php");
		
	}
?>

<!DOCTYPE html>
<html lang="en">

<?php echo cruddy_head() ?>

<body>
<div class="container">
	<div class="span10 offset1">
		<div class="row">
			<h3>Delete a Customer</h3>
		</div>
		
		<form class="form-horizontal" action="delete.php" method="post">
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<p class="alert alert-error">Are you sure to delete ?</p>
			<div class="form-actions">
				<button type="submit" class="btn btn-danger">Yes</button>
				<a class="btn" href="index.php">No</a>
			</div>
		</form>
	</div>
</div> <!-- /container -->
</body>
</html>
