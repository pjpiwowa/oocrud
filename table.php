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
	 * (as a string). $new_fields is an array where the keys are the names
	 * of the fields in the database, and the values are their human-
	 * readable descriptions. When the rows are output in HTML format, the
	 * order used will be the same as that provided in this array.
	 *
	 * IMPORTANT: new_name and all elements of $new_fields must be TRUSTED,
	 * NOT user-supplied. SQL injection may result if this condition is not
	 * observed.
	 */
	public function __construct(/* Database */ $new_db, /* string */ $new_name,
	                            /* array from string to string */ $new_fields)
	{
		$this->db = $new_db;
		$this->name = $new_name;
		$this->fields = $new_fields;
	}

	/*
	 * Add a new row. $row is expected to be an array where the indices are
	 * taken from $this->fields's indices.
	 *
	 * IMPORTANT: ALL of the keys and values in $row MUST be TRUSTED, NOT
	 * user-supplied, or SQL injection may result.
	 */
	public function add_row(/* array from string to string */ $row)
	{
		/*
		 * We need to use $this->name in a string, but because php is
		 * terrible, it is not possible without making a local
		 * variable out of it (or using unnecessary . string . conta .
		 * tena . tion, which is also terrible).
		 */
		$name = $this->name;

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
		$cmd = "INSERT INTO $name (" . implode(", ", $keys) . ") " .
		       "VALUES (" . implode(", ", $vals) . ");";
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
		$name = $this->name;
		
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
		$cmd = "UPDATE $name SET " . implode(", ", $pairs) . " WHERE id = $id;";
		$pdo = $this->db->connect();
		$pdo->query($cmd);
	}

	/*
	 * Delete the row matching $id.
	 */
	public function del_row (/* integer */ $id)
	{
		$name = $this->name;

		$pdo = $this->db->connect();
		//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$cmd = "DELETE FROM $name  WHERE id = $id";
		$pdo->query($cmd);
	}

	/*
	 * Return the row matching $id in the field => value array format
	 * expected by add_row and mod_row.
	 */
	public function get_row(/* integer */ $id)
	{
		$name = $this->name;

		/*
		 * SQL INJECTION if you idiotically
		 * passed a user-supplied string for
		 * name.
		 */
		$sql = "SELECT * FROM $name WHERE id = $id";
		$pdo = $this->db->connect();
		$row = $pdo->query($sql);

		return $row->fetch();
	}

	/* List table contents in HTML format. */
	public function dump()
	{
		$name = $this->name;

		/* Table header (from members)*/
		$ret = '
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
		';

		foreach (array_values($this->fields) as $field)
		{
			
			echo "<th>$field</th>\n";
		}

		echo '
				</tr>
			</thead>
			<tbody>';

			/* Table rows (from database) */
			$pdo = $this->db->connect();

			/*
			 * SQL INJECTION if you idiotically
			 * passed a user-supplied string for
			 * name.
			 */
			$sql = "SELECT * FROM $name ORDER BY id DESC";
			foreach ($pdo->query($sql) as $row)
			{
				$ret = $ret . '<tr>
				';
				foreach (array_keys($this->fields) as $field)
				{
					$ret = $ret . '<td>' . $row[$field] . '<td> ';
				}
				$ret = $ret . '
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
		$name = $this->name;

		$pdo = $this->db->connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM $name where id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$row = $q->fetch(PDO::FETCH_ASSOC);

		$ret = '<div class="form-horizontal" >';
		foreach ($this->fields as $key => $val)
		{
			$ret = $ret . '
			<div class="control-group">
				<label class="control-label">' . $val . '</label>
				<div class="controls">
					<label class="checkbox">
					' . $row[$key] . '
					</label>
				</div>
			</div>
			';
		}
		$ret = $ret . '
			<div class="form-actions">
				<a class="btn" href="' . $back . '">Back</a>
			</div>
		</div>
		';
		return $ret;
	}
}

?>
