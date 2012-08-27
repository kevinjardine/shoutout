<?php

$video_id = $vars['entity']->video_id;
$width = $vars['width'];
$height = $vars['height'];

echo "<video width=\"$width\" height=\"$height\" controls=\"\" tabindex=\"0\">
	<source type=\"video/ogg\" src=\"http://giss.tv/dmmdb//contents/$video_id\"></source>
</video>";
