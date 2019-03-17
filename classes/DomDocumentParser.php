<?php
//DomDocumentParser.php
class DomDocumentParser {
    private $doc;

    public function __construct($url){
        $this->links = [];
        $options = [
            'http' => ['method' => 'GET', 'header' => "User-Agent: soorfuBot/0.1\n"],
        ];
        $context = stream_context_create($options);
        //Create a DOM with html content
        $this->doc = new DOMDocument();
        @$this->doc->loadHTML(file_get_contents($url, false, $context));
    }

    public function get_links(){
        return $this->doc->getElementsByTagName('a');
    }

    public function get_title(){
        return $this->doc->getElementsByTagName('title');
    }

    public function get_meta(){
        return $this->doc->getElementsByTagName('meta');
    }

    public function get_images(){
        return $this->doc->getElementsByTagName('img');
    }
}