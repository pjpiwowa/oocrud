<?php

/*
 * Simple helper function to produce our CRUD grid's pages; $output is the
 * contents of the main container.
 */
function cruddy_page($output)
{
	return  "<!DOCTYPE html>" .
	    in_html(cruddy_head() .
	            in_body(in_div($output, "container"))
	);
}

/*
 * Returns our <head> section.
 */

function cruddy_head()
{
	return '
	<head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
		<meta charset="utf-8">
	</head>';
}

/*
 * The in_* family of functions return their $output argument nested inside the
 * HTML tag indicated by their names.
 */

function in_html($output, $lang = "en")
{
	return "<html lang=\"$lang\">\n${output}\n</html>\n";
}

function in_body($output)
{
	return "<body>\n${output}\n</body>";
}

function in_div($output, $class)
{
	return "<div class=\"$class\">\n${output}\n</div>";
}

function in_p($output)
{
	return "<p>\n${output}\n</p>";
}

?>
