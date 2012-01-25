<?php
elgg_load_library('elgg:shoutout');
$guid = get_input('guid',0);
$attached_guid = get_input('attached_guid',0);
$access_id = get_input('access_id',0);
$text = get_input('text');
$attachments = get_input('attachments');
if (shoutout_edit($guid,$attached_guid,$text,$access_id,$attachments)) {
	$response = array('success' => TRUE, 'msg' => elgg_echo('shoutout:edit:success'));
} else {
	$response = array('success' => FALSE, 'msg' => elgg_echo('shoutout:edit:error'));
}

echo json_encode($response);

exit;
