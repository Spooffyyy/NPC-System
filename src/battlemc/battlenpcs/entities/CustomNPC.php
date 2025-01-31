<?php

namespace battlemc\battlenpcs\entities;

use battlemc\battlenpcs\classes\AssignableTag;
use battlemc\battlenpcs\classes\CustomType;
use battlemc\battlenpcs\handler\NPCEventHandler;
use pocketmine\entity\Human;
use pocketmine\world\ChunkLoader;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class CustomNPC extends Human implements ChunkLoader
{

	/** this is the type of the entity
	 * @var CustomType
	 */
	private $type = null;

	/**
	 * @var string
	 * this is the origin entity title
	 */
	private $header = "";

	/**
	 * this is a list of tags given that are currently being applied to the entity
	 * @var AssignableTag[]
	 */
	private $tags = [];


	/**
	 * @var NPCEventHandler
	 */
	private $handler = null;

	public function __construct(World $world, CompoundTag $nbt)
	{
		$pos = $nbt->getListTag("Pos")->getAllValues();
		$x = $pos[0];
		$z = $pos[2];
		if ($world->isChunkLoaded($x >> 4, $z >> 4) === false) {
			$world->loadChunk($x >> 4, $z >> 4);
		}
		$world->registerChunkLoader($this, $x >> 4, $z >> 4, true);
		parent::__construct($world, $nbt);
	}

	public function update()
	{
		$iterate = $this->header;
		foreach ($this->tags as $tag) {
			$iterate .= "\n";
			$iterate .= $tag->getDisplayLayout();
		}
		$this->setNameTag($iterate);
	}


	public function saveNBT(): CompoundTag {
          return parent::saveNBT();
    }


	/**
	 * @return bool
	 */
	public function hasHandler(): bool
	{
		return $this->handler instanceof NPCEventHandler;
	}

	/**
	 * @return NPCEventHandler|null
	 */
	public function getHandler(): ?NPCEventHandler
	{
		return $this->handler;
	}

	public function addTag(AssignableTag $tag)
	{
		$this->tags[$tag->getName()] = $tag;
	}

	public function hasTag(AssignableTag $tag)
	{
		return array_key_exists($tag->getName(), $this->tags);
	}

	public function removeTag(AssignableTag $tag)
	{
		if ($this->hasTag($tag)) {
			unset($this->tags[$tag->getName()]);
		}
	}

	/**
	 * @return CustomType
	 */
	public function getType(): CustomType
	{
		return $this->type;
	}

	/**
	 * @param CustomType $type
	 */
	public function setType(CustomType $type): void
	{
		$this->type = $type;
	}


	public function getHeader(): string
	{
		return $this->header;
	}


	/**
	 * @param string $header
	 */
	public function setHeader(string $header): void
	{
		$this->header = $header;
	}

	/**
	 * @param NPCEventHandler $handler
	 */
	public function setHandler(?NPCEventHandler $handler): void
	{
		$this->handler = $handler;
	}
 ###################################### Chunk Population ######################################
	public function isLoaderActive(): bool{return ($this->isAlive() && !($this->isClosed()));}
	public function onChunkPopulated(Chunk $chunk){}
	public function getLoaderId(): int{return spl_object_id($this);}
	public function onBlockChanged(Vector3 $block){}
	public function onChunkChanged(Chunk $chunk){}
	public function onChunkLoaded(Chunk $chunk){}
	public function onChunkUnloaded(Chunk $chunk){}

}
