<?php
// TODO: add video URL bit
elgg_load_library('elgg:shoutout');
$origin = get_input('origin','shoutout/all');
$shoutout = $vars['entity'];
if ($shoutout) {
	$value = $shoutout->description;
	$guid = $shoutout->guid;
	$access_id = $shoutout->access_id;
	$count = elgg_get_annotations(array('guid' => $guid,'annotation_name' => 'shoutout_attachment','count'=>TRUE));
	$options = array(
		'relationship_guid'=>$guid,
		'relationship' => 'shoutout_attached_entity',
		'limit' => 1,
	);
	$entities = elgg_get_entities_from_relationship($options);
	if ($entities) {
		$attached_guid = $entities[0]->guid;
	} else {
		$attached_guid = 0;
	}
	$video_url = $shoutout->video_url;
} else {
	$value = '';
	$guid = 0;
	$count = 0;
	$attached_guid = $vars['attached_guid'];
	$page_type = get_input('page_type');
	if ($page_type == 'wall') {
		$access_id = ACCESS_FRIENDS;
	} else {
		$access_id = ACCESS_DEFAULT;
	}
	$video_url = '';
}
$body = elgg_view('input/plaintext',array('class'=>'shoutout-countdown','name'=>'shoutout_text', 'value'=>$value));
$body .= <<< __HTML
<div id="shoutout-bottom-wrapper">
<span id="shoutout-countdown-wrapper">
<span class="shoutout-countdown-number" id="shoutout-countdown-remaining"></span>
__HTML;

$body .= ' '.elgg_echo('shoutout:characters_left');
$body .= '</span>';
$body .= '<span id="shoutout-post-wrapper">';
$body .= elgg_view('output/url',
	array(
		'href' => 'javascript:void(0)',
		'class' => 'elgg-button elgg-button-action shoutout-post-button',
		'text' => elgg_echo('shoutout:post_button')
	)
);
$body .= '</span>';
$body .= '</div>';

$access_label = elgg_echo('access').':';
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'shoutout-access-id',
	'value' => $access_id
));

$body .= <<< __HTML
<div class="shoutout-access-box">
	<span class="shoutout-access-label">$access_label</span> $access_input
</div>
__HTML;

$body .= '<div id="shoutout-file-uploader"></div>';
$body .= elgg_view('shoutout/view_attached',array('attached_guid'=>$attached_guid));
$body .= elgg_view('input/hidden',array('id'=>"shoutout-countdown-max",'value'=>500));
$body .= elgg_view('input/hidden',array('id'=>"shoutout-number-of-attachments",'value'=>$count));
$body .= elgg_view('input/hidden',array('name'=>'guid','id'=>"shoutout-guid",'value'=>$guid));
$body .= elgg_view('input/hidden',array('name'=>'origin','id'=>"shoutout-origin",'value'=>$origin));
$body .= elgg_view('input/hidden',array('name'=>'attached_guid','id'=>"shoutout-attached-guid",'value'=>$attached_guid));
$shoutout_video_add = elgg_get_plugin_setting('video_add', 'shoutout');
if ($shoutout_video_add == 'yes') {
	$body .= '<div id="shoutout-video-wrapper">';
	$body .= elgg_view('output/url',
		array(
			'id' => 'shoutout-video-add-button',
			'href' => 'javascript:void(0)',
			'class' => 'elgg-button elgg-button-action',
			'text' => elgg_echo('shoutout:video_button')
		)
	);
	$body .= '<span id="shoutout-video-input-wrapper">';
	$body .= '<span id="shoutout-video-attached-label">'.elgg_echo('shoutout:video:attached').'</span>';
	$body .= ' ';
	$body .= elgg_view('input/text',array('id'=>'shoutout-video-url','name'=>'video_url','value'=>$video_url));
	$body .= elgg_view('output/url',
		array(
			'id' => 'shoutout-video-delete-button',
			'href' => 'javascript:void(0)',
			'text' => elgg_view_icon('delete'),
			'title' => elgg_echo('shoutout:video:delete:title'),
		)
	);
	$body .= '</span>';
	$body .= '</div>';
}
echo $body;
