<?php
elgg_load_library('elgg:shoutout');
$shoutout = $vars['entity'];
if ($shoutout) {
	$value = $shoutout->description;
	$guid = $shoutout->guid;
	$count = $attachments = elgg_get_annotations(array('guid' => $guid,'annotation_name' => 'shoutout_attachment','count'=>TRUE));
} else {
	$value = '';
	$guid = 0;
	$count = 0;
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

$body .= '<div id="shoutout-file-uploader"></div>';
$body .= elgg_view('input/hidden',array('id'=>"shoutout-countdown-max",'value'=>500));
$body .= elgg_view('input/hidden',array('id'=>"shoutout-number-of-attachments",'value'=>$count));
$body .= elgg_view('input/hidden',array('name'=>'guid','id'=>"shoutout-guid",'value'=>$guid));
echo $body;
