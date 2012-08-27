<?php
elgg_load_library('elgg:videolist');
$url = get_input('url');
$url = elgg_trigger_plugin_hook('videolist:preprocess', 'url', array(),$url);
if (!$url) {
	$result = array('error'=>TRUE,'msg'=>elgg_echo('videolist:error:no_url'));
} else {
	$parsedPlatform = videolist_parse_url($url);

	if (!$parsedPlatform) {
		$result = array('error'=>TRUE,'msg'=>elgg_echo('videolist:error:invalid_url'));
	} else {
    	list ($parsed, $platform) = $parsedPlatform;
    	$result = array('error'=>FALSE,'data'=>$platform->getData($parsed),'videotype'=>$platform->getType());
	}
}

echo json_encode($result);

exit;
