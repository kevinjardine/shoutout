<?php

$embedurl = $vars['entity']->embedurl;
$width = $vars['width'];
$height = $vars['height'];

echo "<iframe src=\"$embedurl\" width=\"$width\" height=\"$height\" frameborder=\"0\" allowfullscreen></iframe>";
