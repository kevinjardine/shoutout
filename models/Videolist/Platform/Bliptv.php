<?php

class Videolist_Platform_Bliptv implements Videolist_PlatformInterface
{
    public function getType()
    {
        return "bliptv";
    }

    public function parseUrl($url)
    {
        $parsed = parse_url($url);
        $path = explode('/', $parsed['path']);

        if ($parsed['host'] != 'blip.tv' || count($path) < 3) {
            return false;
        }

        return array(
            'video_id' => $parsed['path'],
        );
    }

    public function getData($parsed)
    {
        $video_id = $parsed['video_id'];

        $buffer = file_get_contents('http://blip.tv'.$video_id.'?skin=rss');
        $xml = new SimpleXMLElement($buffer);

        return array(
            'title' => current($xml->xpath('/rss/channel/item/title')),
            'description' => strip_tags(current($xml->xpath('/rss/channel/item/description'))),
            'thumbnail' => current($xml->xpath('/rss/channel/item/media:thumbnail/@url')),
            'embedurl' => current($xml->xpath('/rss/channel/item/blip:embedUrl')),
        );
    }
}
