<?php

namespace battlemc\battlenpcs\handler\preset;

use battlemc\battlenpcs\handler\NPCEventHandler;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class MessageHandler extends NPCEventHandler
{
	private $message;


	public function onHit(Entity &$entity, EntityDamageByEntityEvent &$event): bool
	{
		$player = $event->getDamager();
		if ($player instanceof Player) {
			$player->sendMessage($this->getMessage());
		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage($message): void
	{
		$this->message = $message;
	}

}
