<?php

namespace battlemc\battlenpcs\utils;

use battlemc\battlenpcs\caches\TagCache;
use battlemc\battlenpcs\caches\TypeCache;
use battlemc\battlenpcs\classes\AssignableTag;
use battlemc\battlenpcs\classes\CustomType;
use pocketmine\utils\Config;
use pocketmine\Server;

class ConfigLoader
{
    private $serverLogger;

    public function __construct()
    {
        $this->serverLogger = Server::getInstance()->getLogger();
    }

    public function load(string $path)
    {
        $config = new Config($path . "config.yml", Config::YAML);
        $customs = $config->get("enabled-customs");
        foreach ($customs as $custom) {
            $name = $custom["name"];
            if (file_exists($path . $name . "_geometry.json")) {
                $geometry = file_get_contents($path . $name . "_geometry.json");
                if (file_exists($path . $name . "_skin.png")) {
                    $skinData = $this->getFromPathBytes($path . $name . "_skin.png");
                    $type = new CustomType();
                    $type->setGeometry($geometry);
                    $type->setImageData($skinData);
                    $type->setGeometryName($custom["geometry-name"]);
                    $type->setName($name);
                    TypeCache::add($type);
                } else {
                    $this->serverLogger->warning("Could not find Skin File For Custom Entity \"" . $custom . "\"");
                    continue;
                }
            } else {
                $this->serverLogger->warning("Could not find Geometry File For Custom Entity \"" . $custom . "\"");
                continue;
            }
        }
        foreach($config->get("tags") as $tagData){
            if($tagData["enabled"] === true){
                $tag = new AssignableTag();
                $tag->setName($tagData["name"]);
                $tag->setDisplayLayout($tagData["display-layout"]);
                TagCache::add($tag);
            }
        }
    }

    public function getFromPathBytes(string $path): string
    {
        $img = imagecreatefrompng($path);
        $bytes = '';
        $l = getimagesize($path);
        for ($y = 0; $y < $l[1]; $y++) {
            for ($x = 0; $l[0]; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        return $bytes;
    }
}
