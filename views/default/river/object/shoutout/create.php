<?php
/**
 * Shoutout river view.
 */

elgg_load_library('elgg:shoutout');

$object = $vars['item']->getObjectEntity();
$excerpt = parse_urls(elgg_get_excerpt($object->description));
if (substr($excerpt,strlen($excerpt)-3) == '...') {
	$more_bit = ' <a href="'.$object->getURL().'">'.elgg_echo('shoutout:more').'</a>';
	$excerpt .= $more_bit;
}

$excerpt .= shoutout_get_attachment_listing($object);

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'message' => $excerpt,
));

echo shoutout_list_attached_entities($object);
