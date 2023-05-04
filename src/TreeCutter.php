<?php

declare(strict_types=1);

namespace TreeCutter;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\Player;
use pocketmine\item\Item;

class Main extends PluginBase implements Listener
{
    /** @var bool[] $isInUse */
    private $isInUse;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(): void
    {
        parent::onDisable();
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if (isset($this->isInUse[$event->getPlayer()->getName()]))
            return;
        $player = $event->getPlayer();
        $block = $event->getBlock();
  
        if ($block instanceof \pocketmine\block\Log) {
            $this->isInUse[$player->getName()] = true;
            $this->breakTree($block, $event->getItem(), $player);
            unset($this->isInUse[$player->getName()]);
        }
    }

    public function breakTree(Block $block, Item $item, \pocketmine\player\Player $player, array &$dont = []): void {
        if ($block instanceof \pocketmine\block\Log || $block instanceof \pocketmine\block\Leaves) {
            $dont[] = $block->getPosition()->asVector3()->__toString();
            foreach($block->getAllSides() as $side) {
                if(($side instanceof \pocketmine\block\Log || $side instanceof \pocketmine\block\Leaves) and !in_array($side->getPosition()->asVector3()->__toString(), $dont)) {
                    $this->breakTree($side, $item, $player, $dont);
                }
            }
            $player->getWorld()->useBreakOn($block->getPosition(), $item, $player, true);
        }
    }
}