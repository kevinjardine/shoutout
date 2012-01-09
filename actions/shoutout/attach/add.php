<?php

// load plugin model
elgg_load_library('elgg:shoutout');

// save the attachment to the user's attachment directory
// and return the filestore name

$original_name = get_input('qqfile');
$result = shoutout_attach_add($original_name);
echo $result;
exit;
