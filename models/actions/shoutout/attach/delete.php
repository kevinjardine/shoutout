<?php
elgg_load_library('elgg:shoutout');
$guid = get_input('guid');
$time_bit = get_input('time_bit');
$ofn = get_input('filename');
if (elgg_is_admin_logged_in() || (elgg_get_logged_in_user() == $guid)) {
	shoutout_attach_delete($guid, $time_bit, $ofn);
}

exit;