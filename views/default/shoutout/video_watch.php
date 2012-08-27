<?php
$guid = $vars['guid'];
$e = get_entity($guid);
if (elgg_instanceof($e,'object','shoutout')) {
	$content = elgg_view("shoutout/watch/{$e->videotype}", array(
		'entity' => $e,
		'width' => 600,
		'height' => 400,
	));
	$content = "<div class=\"shoutout-video-watch\">$content</div>";
	echo $content;
}
