<?php

$embedurl = $vars['entity']->embedurl;
$width = $vars['width'];
$height = $vars['height'];

echo "<embed flashVars=\"playerVars=autoPlay=no\" src=\"$embedurl\" width=\"540\" height=\"304\" wmode=\"transparent\" allowFullScreen=\"true\" allowScriptAccess=\"always\" name=\"Metacafe_$video_id\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed>";
