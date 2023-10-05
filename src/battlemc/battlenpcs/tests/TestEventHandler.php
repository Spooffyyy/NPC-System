<?php

namespace battlemc\battlenpcs\tests;

use battlemc\battlenpcs\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class TestEventHandler extends NPCEventHandler
{
    public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
    {
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $damager->sendMessage("lol");
        }
        return false;
    }
}
