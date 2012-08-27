<?php

class Videolist_Platform_Metacafe implements Videolist_PlatformInterface
{
    public function getType()
    {
        return "metacafe";
    }

    public function parseUrl($url)
    {
        $parsed = parse_url($url);
        $path = explode('/', $parsed['path']);

        if ($parsed['host'] != 'www.metacafe.com' || $path[1] != 'watch' || !(int) $path[2]) {
            return false;
        }

        return array(
            'video_id' => $path[2],
        );
    }

    public function getData($parsed)
    {
        $video_id = $parsed['video_id'];

        $buffer = file_get_contents("http://www.metacafe.com/api/item/$video_id");
        $xml = new SimpleXMLElement($buffer);

        return array(
            'title' => current($xml->xpath('/rss/channel/item/title')),
            'description' => strip_tags(current($xml->xpath('/rss/channel/item/description'))),
            'thumbnail' => current($xml->xpath('/rss/channel/item/media:thumbnail/@url')),
            'embedurl' => current($xml->xpath('/rss/channel/item/media:content/@url')),
        );
    }
}
