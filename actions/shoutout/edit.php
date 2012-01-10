<?php
elgg_load_library('elgg:shoutout');
$guid = get_input('guid',0);
$text = get_input('text');
$attachments = get_input('attachments');
if ($content = shoutout_edit($guid,$text,$attachments)) {
	$response = array('success' => TRUE, 'msg' => elgg_echo('shoutout:edit:success'), 'html'=>$content);
} else {
	$response = array('success' => FALSE, 'msg' => elgg_echo('shoutout:edit:error'));
}

echo json_encode($response);

exit;
