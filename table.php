<?php

require 'database.php';

// Quote a string; used in SQL generation
function quote(&$str, $dummy)
{
	$str = "\"$str\"";
}

/*
 * Represents one abstract table, stored in a Database object. It is assumed
 * that there is an auto-incrementing primary key named id; the names of other
 * fields must be provided at construction time.
 */
class cruddy_table
{
	private $db;
	private $name;

	// Not including id.
	private $fields;

	// $this->fields is read-only, the others are inaccessible
	public function fields()
	{
		return $this->fields;
	}

	/*
	 * $new_db should be a Database object. $new_name is the name of the table
	 * (as a string). $new_fields is an array containing the names of the
	 * table's fields other than the primary 'id' key; when the rows are
	 * output in HTML format, the order used will be the same as that provided
	 * in this array.
	 *
	 * IMPORTANT: new_name and all elements of $new_fields must be TRUSTED,
	 * NOT user-supplied. SQL injection may result if this condition is not
	 * observed.
	 */
	public function __construct(/* Database */ $new_db, /* string */ $new_name,
	                            /* array of string */ $new_fields)
	{
		$this->db = $new_db;
		$this->name = $new_name;
		$this->fields = $new_fields;
	}

	/*
	 * Add a new row. $row is expected to be an array where the indices are
	 * taken from $this->fields.
	 *
	 * IMPORTANT: ALL of the keys and values in $row MUST be TRUSTED, NOT
	 * user-supplied, or SQL injection may result.
	 */
	public function add_row(/* array from string to string */ $row)
	{
		// Quote all names
		$keys = array_keys($row);
		$vals = array_values($row);
		array_walk($vals, 'quote');
		/*
		 * SQL INJECTION if you idiotically
		 * passed a user-supplied string for
		 * name, or user-supplied strings in
		 * $row.
		 */
		$cmd = "INSERT INTO $this->name (" . implode(", ", $keys) . ") " .
		       "VALUES (" . implode(", ", $vals) . ");";
		       echo $cmd;
		$pdo = $this->db->connect();
		$pdo->query($cmd);
	}

	/*
	 * Modify an existing row. $id is the ID of the row to modify, $row is
	 * an array in the same format as that expected by add_row above.
	 *
	 * IMPORTANT: ALL of the keys and values in $row MUST be TRUSTED, NOT
	 * user-supplied, or SQL injection may result.
	 */
	public function mod_row(/* integer */ $id, /* array from string to string */ $row)
	{
		// Quote all names
		$keys = array_keys($row);
		$vals = array_values($row);
		array_walk($vals, 'quote');

		$pairs = array();
		foreach (array_combine($keys, $vals) as $key => $val)
		{
			array_push($pairs, "$key=$val");
		}

		/*
		 * SQL INJECTION if you idiotically
		 * passed a user-supplied string for
		 * name, or user-supplied strings in
		 * $row.
		 */
		$cmd = "UPDATE $this->name SET " . implode(", ", $pairs) . " WHERE id = $id;";
		$pdo = $this->db->connect();
		$pdo->query($cmd);
	}

	/*
	 * Return the row matching $id in the field => value array format
	 * expected by add_row and mod_row.
	 */
	public function get_row(/* integer */ $id)
	{
		/*
		 * SQL INJECTION if you idiotically
		 * passed a user-supplied string for
		 * name.
		 */
		$sql = "SELECT * FROM $this->name WHERE id = $id";
		$pdo = $this->db->connect();
		$row = $pdo->query($sql);

		return $row->fetch();
	}

	/* List table contents in HTML format. */
	public function dump()
	{
		/* Table header (static) */
		$ret = '
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Email Address</th>
					<th>Mobile Number</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>';

			/* Table rows  (dynamically generated) */
			$pdo = $this->db->connect();

			/*
			 * SQL INJECTION if you idiotically
			 * passed a user-supplied string for
			 * name.
			 */
			$sql = "SELECT * FROM $this->name ORDER BY id DESC";
			foreach ($pdo->query($sql) as $row)
			{
				$ret = $ret . '<tr>
					<td>' . $row['name'] . '</td>
					<td>' . $row['email'] . '</td>
					<td>' . $row['mobile'] . '</td>
					<td width="250">
						<a class="btn" href="read.php?id=' . $row['id'] . '">Read</a>
						<a class="btn" href="update.php?id=' . $row['id'] . '">Update</a>
						<a class="btn" href="delete.php?id=' . $row['id'] . '">Delete</a>
					</td>
				</tr>';
			}
			$ret = $ret . '
			</tbody>
		</table>
		';
		return $ret;
	}

	/* Output the row with a given ID as HTML. */
	public function dump_row(/* integer */ $id, /* string */ $back = "index.php")
	{
		$pdo = $this->db->connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM customers where id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$row = $q->fetch(PDO::FETCH_ASSOC);

		return '
		<div class="form-horizontal" >
			<div class="control-group">
				<label class="control-label">Name</label>
				<div class="controls">
					<label class="checkbox">
					' . $row['name'] . '
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Email Address</label>
				<div class="controls">
					<label class="checkbox">
						' . $row['email'] . '
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Mobile Number</label>
				<div class="controls">
					<label class="checkbox">
						' . $row['mobile'] . '
					</label>
				</div>
			</div>
			<div class="form-actions">
				<a class="btn" href="' . $back . '">Back</a>
			</div>
		</div>
		';
	}
}

?>
