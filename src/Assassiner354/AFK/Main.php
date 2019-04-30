<?php

namespace Assassiner354\AFK;

//Important
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

//Events
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener {

  public afk = [];
  
  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  public function onQuit(PlayerQuitEvent $event) {
    $player = $event->getPlayer();
    if(isset($this->afk[$player->getName()])) {
      unset($this->afk[$player->getName()]);
    }
  }
  
  public function onMove(PlayerMoveEvent $event) {
    $player = $event->getPlayer();
    $name = $player->getName();
    if(isset($this->afk[$player->getName()])) {
      unset($this->afk[$player->getName()]);
      $player->setDisplayName($name);
      $player->sendMessage(TF::GREEN . "You are no longer AFK!");
    }
  }
  public function onDamage(EntityDamageEvent $event) {
    if($event->getEntity() instanceof Player && isset($this->afk[$player->getName()])) {
      $event->setCancelled(true);
    }
  }
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
    switch($cmd->getName()) {
      case "afk":
        if(!$sender instanceof Player) {
          $sender->sendMessage(TF::RED . "This command can only be used in-game!");
          return true;
        }
        
        $name = $sender->getName();
        if(isset($this->afk[$player->getName()])) {
          unset($this->afk[$player->getName()]);
          $player->setDisplayName($name);
          $player->sendMessage(TF::GREEN . "You are no longer AFK!");
        } else {
          this->afk[] = $name;
          $sender->setDisplayName(TF::YELLOW . "[AFK] " . $name);
          $sender->sendMessage(TF::GREEN . "You are now AFK!");
        }
        break;
    }
    return true;
  }
}
