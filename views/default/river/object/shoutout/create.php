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

$options = array(
	'relationship_guid'=>$object->guid,
	'relationship' => 'shoutout_attached_entity',
	'limit' => 1,
);
$entities = elgg_get_entities_from_relationship($options);
if ($entities) {
	$entity = $entities[0];
	if (elgg_instanceof($entity,'object','poll')) {
		if (elgg_plugin_exists('polls')) {
			if ($user_guid = elgg_get_logged_in_user_guid()) {
				elgg_load_library('elgg:polls');
				$can_vote = !polls_check_for_previous_vote($entity, $user_guid);
				if ($can_vote) {
					echo elgg_view('polls/poll_widget',array('entity'=>$entity));
				} else {
					echo elgg_echo('shoutout:see_poll_results').elgg_view('polls/summary_link',array('entity'=>$entity));
				}
			} else {
				echo elgg_echo('shoutout:login_or_see_poll_results').elgg_view('polls/summary_link',array('entity'=>$entity));
			}
		}
	}
}
