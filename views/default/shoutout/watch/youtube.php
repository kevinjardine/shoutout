<?php

$video_id = $vars['entity']->video_id;
$width = $vars['width'];
$height = $vars['height'];

echo "<iframe width=\"$width\" height=\"$height\" src=\"https://www.youtube-nocookie.com/embed/$video_id\" frameborder=\"0\" allowfullscreen></iframe>";
