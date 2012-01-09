<?php
// TODO - make this ajaxy?
elgg_load_library('elgg:shoutout');
$guid = get_input('guid');
if (shoutout_delete($guid)) {
	system_message(elgg_echo('shoutout:delete:success'));
} else {
	register_error(elgg_echo('shoutout:delete:error'));
}

forward('shoutout/activity');
