<?php
class Crawler {

protected $markup = ”;

public function __construct($uri) {

$this->markup = $this->getMarkup($uri);

}

public function getMarkup($uri) {

return file_get_contents($uri);

}

public function get($type) {

$method = “_get_{$type}”;

if (method_exists($this, $method)){

return call_user_method($method, $this);

}

}

protected function _get_images() {

if (!empty($this->markup)){

preg_match_all("/<img([^>]+)\/>/i", $this->markup, $images);

return !empty($images[1]) ? $images[1] : FALSE;

}

}

protected function _get_links() {

if (!empty($this->markup)){

preg_match_all("/<a([^>]+)\>(.*?)\<\/a\>/i", $this->markup, $links);

return !empty($links[1]) ? $links[1] : FALSE;

}

}

}



$crawl = new Crawler("http://vision-media.ca");

$images = $crawl->get(‘images’);

$links = $crawl->get(‘links’);

?>