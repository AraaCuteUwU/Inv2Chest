<?php

namespace FiraAja\Inv2Chest;

use pocketmine\block\Chest;
use pocketmine\block\tile\Chest as ChestTile;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() == "unload") {
            if (!$sender instanceof Player) return false;
            if (!$sender->hasPermission("inv2chest.command")) {
                $sender->sendMessage($this->getConfig()->get("no-permission"));
                return false;
            }
            $targetBlock = $sender->getTargetBlock($sender->getViewDistance());
            if ($targetBlock instanceof Chest) {
                $blockTile = $sender->getWorld()->getTile($targetBlock->getPosition()->floor());
                if ($blockTile instanceof ChestTile) {
                    $inventory = $blockTile->getInventory();
                    foreach ($sender->getInventory()->getContents() as $content) {
                        if ($inventory->canAddItem($content)) {
                            $inventory->addItem($content);
                            $sender->getInventory()->remove($content);
                        } else {
                            $sender->sendMessage($this->getConfig()->get("chest-full"));
                        }
                    }
                }
            } else {
                $sender->sendMessage($this->getConfig()->get("not-a-chest"));
            }
        }
        return false;
    }
}