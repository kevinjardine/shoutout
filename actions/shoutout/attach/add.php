<?php

// load plugin model
elgg_load_library('elgg:shoutout');

// save the attachment to the user's attachment directory
// and return the filestore name

$original_name = urldecode(get_input('qqfile'));
if (!$original_name) {
	// try $_FILES instead
	if (isset($_FILES)) {
		$original_name = $_FILES['qqfile']['name'];
	}
}
$result = shoutout_attach_add($original_name);

echo $result;
exit;
