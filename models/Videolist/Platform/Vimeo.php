<?php

class Videolist_Platform_Vimeo implements Videolist_PlatformInterface
{
    public function getType()
    {
        return "vimeo";
    }

    public function parseUrl($url)
    {
        $parsed = parse_url($url);
        $path = explode('/', $parsed['path']);

        if ($parsed['host'] != 'vimeo.com' || !(int) $path[1]) {
            return false;
        }

        return array(
            'video_id' => $path[1],
        );
    }

    public function getData($parsed)
    {
        $video_id = $parsed['video_id'];

        $buffer = file_get_contents("http://vimeo.com/api/v2/video/$video_id.xml");
        $xml = new SimpleXMLElement($buffer);

        $videos = $xml->children();
        $video = $videos[0];

        return array(
            'title' => $video->title,
            'description' => strip_tags($video->description),
            'thumbnail' => $video->thumbnail_medium,
        );
    }
}
