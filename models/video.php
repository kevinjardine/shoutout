<?php

/**
 * @return array
 */
function videolist_get_default_platforms() {
    static $platforms = array();
    if (! $platforms) {
        require dirname(__FILE__) . '/Videolist/PlatformInterface.php';
        $path = dirname(__FILE__) . '/Videolist/Platform';
        foreach (scandir($path) as $filename) {
            if (preg_match('/^(\\w+)\\.php$/', $filename, $m)) {
                require "$path/$filename";
                $class = 'Videolist_Platform_' . $m[1];
                $platform = new $class();
                if ($platform instanceof Videolist_PlatformInterface) {
                    /* @var Videolist_PlatformInterface $platform */
                    $platforms[$platform->getType()][] = $platform;
                }
            }
        }
    }
    return $platforms;
}

/**
 * @param string $url
 * @return array [parsed, platform]
 */
function videolist_parse_url($url) {
    $params = array(
        'url' => $url,
    );
    $platforms = videolist_get_default_platforms();
	$platforms = elgg_trigger_plugin_hook('videolist:prepare', 'platforms', $params, $platforms);
    foreach ($platforms as $list) {
        foreach ($list as $platform) {
            /* @var Videolist_PlatformInterface $platform */
            $parsed = $platform->parseUrl($url);
            if ($parsed) {
                return array($parsed, $platform);
            }
        }
    }
	return false;
}
