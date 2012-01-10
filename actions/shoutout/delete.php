<?php
// AJAXy delete action
elgg_load_library('elgg:shoutout');
$guid = get_input('guid');
if (shoutout_delete($guid)) {
	$response = array('success' => TRUE, 'msg' => elgg_echo('shoutout:delete:success'));
} else {
	$response = array('success' => FALSE, 'msg' => elgg_echo('shoutout:delete:error'));
}

echo json_encode($response);

exit;