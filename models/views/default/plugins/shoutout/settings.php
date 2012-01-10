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

$shoutout_max_attachments = elgg_get_plugin_setting('max_attachments', 'shoutout');
if (!$shoutout_max_attachments) {
	$shoutout_max_attachments = -1;
}

$body .= elgg_echo('shoutout:settings:max_attachments:title');
$body .= '<br />';
$body .= elgg_view('input/text',array('name'=>'params[max_attachments]','value'=>$shoutout_max_attachments));

echo $body;
