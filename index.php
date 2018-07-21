<?php

require 'table.php';
require 'util.php';
require 'config.php';

$db = new Database($DB_NAME, $DB_HOST, $DB_USER, $DB_PASS);
$tbl = new cruddy_table($db, "customers", array("name", "email", "mobile"));

echo cruddy_page(in_div("<h3>Dumpster Fire CRUD Grid</h3>", "row") .
                 in_div(in_p("<a href=\"create.php\" class=\"btn btn-success\">Create</a>") .
                 $tbl->dump(), "row"));
