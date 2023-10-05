<?php

namespace battlemc\battlenpcs\classes;

use battlemc\battlenpcs\entities\CustomNPC;
use battlemc\battlenpcs\handler\NPCEventHandler;
use pocketmine\entity\Skin;
use pocketmine\world\World;
use pocketmine\math\Vector3;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ByteArrayTag;

class NPCBuilder
{
    private $name;
    private $tags = [];
    private $type;
    private $position;
    private $world;
    private $handler = null;

    public function setType(CustomType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function addTag(AssignableTag $tag): self
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setWorld(World $world): self
    {
        $this->world = $world;
        return $this;
    }

    public function setPosition(Vector3 $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function setHandler(NPCEventHandler $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function build(): CustomNPC
    {
        if ($this->world instanceof World && $this->position instanceof Vector3 && $this->name !== null && $this->name !== "") {
            $skin = new Skin(uniqid(), $this->getType()->getImageData(), "", $this->getType()->getGeometryName(), $this->getType()->getGeometry());
            $nbt = CustomNPC::saveNBT();
            $skinTag = new CompoundTag("Skin");
            $skinTag->setTag("Name", new StringTag("Name", $skin->getSkinId()));
            $skinTag->setTag("Data", new ByteArrayTag("Data", $skin->getSkinData()));
            $skinTag->setTag("CapeData", new ByteArrayTag("CapeData", $skin->getCapeData()));
            $skinTag->setTag("GeometryName", new StringTag("GeometryName", $skin->getGeometryName()));
            $skinTag->setTag("GeometryData", new ByteArrayTag("GeometryData", $skin->getGeometryData()));
            $nbt->setTag("Skin", $skinTag);
            $npc = new CustomNPC($this->world, $nbt);
            foreach ($this->tags as $tag) {
                $npc->addTag($tag);
            }
            $npc->setHeader($this->name);
            $npc->setHandler($this->handler);
            $npc->update();
            return $npc;
        }

        throw new InvalidArgumentException("It seems at least one of the required arguments (World, Position, Header) for your NPC Build is missing");
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): CustomType
    {
        return $this->type;
    }

    public function getPosition(): Vector3
    {
        return $this->position;
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }
}
