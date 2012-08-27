<?php
$e = $vars['entity'];
?>
<div class="shoutout-video-thumbnail-wrapper">
<a class="shoutout-video-thumbnail" href="javascript:void(0)" title="<?php echo elgg_echo('shoutout:video:play:title'); ?>" rel="<?php echo $e->guid; ?>"><img border="0" src="<?php echo $e->thumbnail; ?>"></a>
</div>
