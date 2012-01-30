<?php
/**
 * Shoutout river view.
 */

elgg_load_library('elgg:shoutout');

$object = $vars['item']->getObjectEntity();
$excerpt = parse_urls(elgg_get_excerpt($object->description));
$excerpt .= shoutout_get_attachment_listing($object);

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'message' => $excerpt,
));

echo shoutout_list_attached_entities($object);
