<?php
//crawl.php
include 'config.php';
include 'classes/DomDocumentParser.php';

$alreadyCrawled = [];
$crawling = [];
$alreadyFoundImages = [];

function check_url_exist($url){
    global $connection;
    $sql = "select count(url) from Sites where url = :url";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':url', $url);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function insert_link($url, $title, $description, $keywords){
    global $connection;
    if(!check_url_exist($url)){
        $sql = "insert into Sites (url, title, description, keywords) values (:url, :title, :description, :keywords)";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':keywords', $keywords);
        return $stmt->execute();
    }
}

function check_imgSrc_exist($src){
    global $connection;
    $sql = "select count(src) from Images where src = :src";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':src', $src);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function insert_image($siteUrl, $src, $title, $alt){
    global $connection;
    if(!check_imgSrc_exist($src)){
        $sql = "insert into Images (siteUrl, src, title, alt) values (:siteUrl, :src, :title, :alt)";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':siteUrl', $siteUrl);
        $stmt->bindParam(':src', $src);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':alt', $alt);
        return $stmt->execute();
    }
}

function create_link($src, $url){
    $parsedUrl = parse_url($url);
    $scheme = $parsedUrl['scheme'];
    $host = $parsedUrl['host'];
    if(substr($src, 0, 2) == '//'){
        // www.dantri.com
        $src = $scheme . ':' . $src;
    } elseif (substr($src,0, 1) == '/') {
        // /thoisu/hoaky.html
        $src = $scheme . '://' . $host . $src;
    } elseif (substr($src,0, 2) == './') {
        // ./thoisu/hoaky.html
        $src = $scheme . '://' . $host . dirname($parsedUrl['path']) . substr($src, 1);
    } elseif (substr($src,0, 3) == '../') {
        // ../thoisu/hoaky.html
        $src = $scheme . '://' . $host . '/' . $src;
    } elseif (substr($src,0, 5) != 'https' && substr($src,0, 4) != 'http') {
        // thoisu/hoaky.html
        $src = $scheme . '://' . $host . '/' . $src;
    }
    return $src;
}

function get_details($url){
    global $alreadyFoundImages;
    $domParser = new DomDocumentParser($url);
    $titles = $domParser->get_title();
    if(sizeof($titles) == 0 || $titles->item(0) == null)
        return;
    $title = $titles->item(0)->nodeValue;
    $title = str_replace('\n', '', $title);
    if(empty($title))
        return;

    $description = '';
    $keywords = '';
    $metas = $domParser->get_meta();
    foreach ($metas as $meta){
        if($meta->getAttribute('name') == 'description')
            $description = $meta->getAttribute('content');
        if($meta->getAttribute('name') == 'keywords')
            $keywords = $meta->getAttribute('content');
    }
    $description = str_replace('\n', '', $description);
    $keywords = str_replace('\n', '', $keywords);
    insert_link($url, $title, $description, $keywords);

    $images = $domParser->get_images();
    foreach ($images as $image){
        $imgSrc = create_link($image->getAttribute('src'), $url);
        $imgTitle =$image->getAttribute('title');
        $imgAlt = $image->getAttribute('alt');
        if(empty($imgTitle) && empty($imgAlt))
            continue;
        if(!in_array($imgSrc, $alreadyFoundImages)) {
            array_push($alreadyFoundImages, $imgSrc);
            insert_image($url, $imgSrc, $imgTitle, $imgAlt);
        }
    }
}

function follow_links($url){
    global $alreadyCrawled;
    global $crawling;
    $domParser = new DomDocumentParser($url);
    $links = $domParser->get_links();
    foreach ($links as $link){
        $href =$link->getAttribute('href');
        if((strpos($href,'#') !== false) || (substr($href, 0, 11) == 'javascript:') || empty($href)){
            continue;
        }
        $href = create_link($href, $url);
        if(!in_array($href, $alreadyCrawled)) {
            array_push($alreadyCrawled, $href);
            array_push($crawling, $href);
            get_details($href);
        }
    }
    array_shift($crawling);
    foreach ($crawling as $site){
        follow_links($site);
    }
}

$startUrl = "http://reecekenney.com/";
follow_links($startUrl);
