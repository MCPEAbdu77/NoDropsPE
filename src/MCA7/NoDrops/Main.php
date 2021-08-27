<?php

declare(strict_types=1);

namespace MCA7\NoDrops;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDrop(PlayerDropItemEvent $e){
		if($this->getConfig()->get("enabled") === true){
			$e->getPlayer()->sendMessage("§cYou are not allowed to drop items! \n §cUse §e/drop §cto drop the item!");
			$e->setCancelled();
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		switch (strtolower($command->getName()))
		{
			case 'toggledrops':
				if(!$sender instanceof Player OR $sender->hasPermission("nodrops.command")) {
					if ($this->getConfig()->get("enabled") === true) {
						$this->getConfig()->set("enabled", false);
						$this->getConfig()->save();
						$sender->sendMessage("§aToggled drops (disabled)");
					} else {
						$this->getConfig()->set("enabled", true);
						$this->getConfig()->save();
						$sender->sendMessage("§aToggled drops (enabled)");
					}
				} else {
					$sender->sendMessage("§cYou do not have the permission!");
				}
				return true;
			case 'trash':
			case 'trashbin':
				if($sender instanceof Player){
					$trash = $sender->getInventory()->getItemInHand();
					$sender->getInventory()->removeItem($trash);
					$sender->sendMessage("§aRemoved item in hand to trashbin!");
				} else {
					$sender->sendMessage("Execute in-game!");
				}
				return true;
			case 'dropitem':
			case 'drop':
				if($sender instanceof Player){
					$item = $sender->getInventory()->getItemInHand();
					if(!$item->getId() == 0) {
						$lvl = $sender->getLevel();
						$sender->getInventory()->removeItem($item);
						$dv = $sender->getDirectionVector()->multiply(0.5);
						$dv->x = $dv->x; $dv->y = 0; $dv->z = $dv->z;
						$lvl->dropItem($sender->getPlayer()->asVector3()->add(0, 1, 0), $item, $dv);
						$sender->sendMessage("§aItem dropped!");
					} else {
						$sender->sendMessage("§cHold the item that you want to drop!");
					}
				} else {
					$sender->sendMessage("Execute in-game only!");
				}
		}
		return true;
	}
}
