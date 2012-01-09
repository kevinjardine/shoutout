<?php
/**
 * Shoutout river view.
 */

elgg_load_library('elgg:shoutout');

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = elgg_get_excerpt($excerpt);
$excerpt .= shoutout_get_attachment_listing($object);

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'message' => $excerpt,
));