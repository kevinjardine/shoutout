<?php

interface Videolist_PlatformInterface {
    /**
     * @abstract
     * @return string
     */
    public function getType();

    /**
     * @abstract
     * @param string $url
     * @return array
     */
    public function parseUrl($url);

    /**
     * @abstract
     * @param array $parsed
     * @return array
     */
    public function getData($parsed);
}
