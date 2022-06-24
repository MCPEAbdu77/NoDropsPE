<?php

declare(strict_types=1);

namespace MCA7\NoDrops;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

	public function onEnable(): void
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}


	public function onDrop(PlayerDropItemEvent $e): void
	{
		$config = $this->getConfig();
		if ($config->get("enabled") === true) {
			$e->getPlayer()->sendMessage("§cYou are not allowed to drop items! \n §cUse §e/drop §cto drop the item!");
			$e->cancel();
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		switch (strtolower($command->getName())) {
			case 'toggledrops':
				if (!get_class($sender) == "pocketmine\player\Player" or $sender->hasPermission("nodrop.command")) {
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
				if (get_class($sender) == "pocketmine\player\Player") {
					$trash = $sender->getInventory()->getItemInHand();
					$sender->getInventory()->removeItem($trash);
					$sender->sendMessage("§aRemoved item in hand to trashbin!");
				} else {
					$sender->sendMessage("Execute in-game!");
				}
				return true;
			case 'dropitem':
			case 'drop':
				if (get_class($sender) == "pocketmine\player\Player") {
					if ($this->getConfig()->get("drop-command") === true) {
						$item = $sender->getInventory()->getItemInHand();
						if (!$item->getId() == 0) {
							$sender->getInventory()->removeItem($item);
							$sender->dropItem($item);
							$sender->sendMessage("§aItem dropped!");
						} else {
							$sender->sendMessage("§cHold the item that you want to drop!");
						}
					} else {
						$sender->sendMessage("§cThis command has been disabled by the server admin§e!");
					}
				} else {
					$sender->sendMessage("Command is only executable in-game!");
				}
		}
		return true;
	}
}
