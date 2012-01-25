<?php
if ($vars['attached_guid']) {
	$entity = get_entity($vars['attached_guid']);
	// currently this code only knows about polls, but it could be extended
	if (elgg_instanceof($entity,'object','poll')) {
		echo elgg_view('shoutout/attached_entity_wrapper', array('content' => elgg_view('polls/widget',array('entity'=>$entity))));
	}
}
