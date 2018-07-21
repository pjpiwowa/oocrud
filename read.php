<?php

require 'table.php';
require 'util.php';
require 'config.php';

$db = new Database($DB_NAME, $DB_HOST, $DB_USER, $DB_PASS);
$tbl = new cruddy_table($db, "customers", array("name", "email", "mobile"));

echo cruddy_page($tbl->dump_row($_GET['id']));

?>
