<?php

class Crawler
{
    public $id;
    public $url;
    public $status;

    public function __construct($id, $url, $status)
    {
        $this->id = $id;
        $this->url = $url;
        $this->status = $status;
    }
}