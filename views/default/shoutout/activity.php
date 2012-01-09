<?php
echo 'activity listing should go here';
print_r(elgg_get_entities(array('type'=>'object','subtype'=>'shoutout','limit'=>1)));