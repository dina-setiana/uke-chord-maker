<?php

class SongLinkPvm
{
    public $Title = '';
    public $Uri = '';

    function __construct($title, $uri)
    {
        $this->Title = $title;
        $this->Uri = $uri;
    }
}
