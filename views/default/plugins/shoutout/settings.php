<?php
/**
 * Settings for shoutout plugin
 */

$yn_options = array(elgg_echo('shoutout:settings:yes')=>'yes',
	elgg_echo('shoutout:settings:no')=>'no',
);

$body = '';

$shoutout_override_activity = elgg_get_plugin_setting('override_activity', 'shoutout');
if (!$shoutout_override_activity) {
	$shoutout_override_activity = 'no';
}

$body .= elgg_echo('shoutout:settings:overide_activity:title');
$body .= '<br />';
$body .= elgg_view('input/radio',array('name'=>'params[override_activity]','value'=>$shoutout_override_activity,'options'=>$yn_options));

$body .= '<br />';

$shoutout_video_add = elgg_get_plugin_setting('video_add', 'shoutout');
if (!$shoutout_video_add) {
	$shoutout_video_add = 'no';
}

$body .= elgg_echo('shoutout:settings:video_add:title');
$body .= '<br />';
$body .= elgg_view('input/radio',array('name'=>'params[video_add]','value'=>$shoutout_video_add,'options'=>$yn_options));

$body .= '<br />';

$shoutout_wall = elgg_get_plugin_setting('wall', 'shoutout');
if (!$shoutout_wall) {
	$shoutout_wall = 'no';
}

$body .= elgg_echo('shoutout:settings:wall:title');
$body .= '<br />';
$body .= elgg_view('input/radio',array('name'=>'params[wall]','value'=>$shoutout_wall,'options'=>$yn_options));

$body .= '<br />';

$shoutout_white_list = elgg_get_plugin_setting('whitelist', 'shoutout');
if (!$shoutout_white_list) {
	$shoutout_white_list = 'gif, png, jpg, jpeg';
}

$body .= elgg_echo('shoutout:settings:whitelist:title');
$body .= '<br />';
$body .= elgg_view('input/text',array('name'=>'params[whitelist]','value'=>$shoutout_white_list));


$body .= '<br /><br />';

$shoutout_size_limit = elgg_get_plugin_setting('size_limit', 'shoutout');
if (!$shoutout_size_limit) {
	$shoutout_size_limit = 2 * 1024 * 1024;
}

$body .= elgg_echo('shoutout:settings:size_limit:title');
$body .= '<br />';
$body .= elgg_view('input/text',array('name'=>'params[size_limit]','value'=>$shoutout_size_limit));


$body .= '<br /><br />';

$shoutout_max_attachments = elgg_get_plugin_setting('max_attachments', 'shoutout');
if (!$shoutout_max_attachments) {
	$shoutout_max_attachments = -1;
}

$body .= elgg_echo('shoutout:settings:max_attachments:title');
$body .= '<br />';
$body .= elgg_view('input/text',array('name'=>'params[max_attachments]','value'=>$shoutout_max_attachments));

echo $body;
