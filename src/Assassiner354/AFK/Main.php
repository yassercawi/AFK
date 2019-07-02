<?php

namespace Assassiner354\AFK;

/*                 AFK Copyright (C) 2019 Assassiner354
               This program comes with ABSOLUTELY NO WARRANTY.
    This is free software, and you are welcome to redistribute it
            under certain conditions; type `show c' for details.

    You should also get your employer (if you work as a programmer) or school,
    if any, to sign a "copyright disclaimer" for the program, if necessary.
    For more information on this, and how to apply and follow the GNU GPL, see
                    <https://www.gnu.org/licenses/>.
*/


//Important
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;

//Events
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener {

  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->afk = array();

    @mkdir($this->getDataFolder());
    $this->saveDefaultConfig();
    $this->getResource("config.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
  }

  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(isset($this->afk[strtolower($player->getName())])) {
      unset($this->afk[strtolower($player->getName())]);
      $player->sendMessage(TF::GREEN . "You have been removed from AFK Mode!");
    }
  }

  public function onQuit(PlayerQuitEvent $event) {
    $player = $event->getPlayer();
    if(isset($this->afk[strtolower($player->getName())])) {
      unset($this->afk[strtolower($player->getName())]);
    }
  }

  public function onMove(PlayerMoveEvent $event) {
    $player = $event->getPlayer();
    $name = $player->getName();
    if(isset($this->afk[strtolower($player->getName())])) {
      $player->sendMessage(TF::RED . "You can't move while AFK!");
      $player->sendMessage(TF::GREEN . "Type /afk to start moving!");
      $event->setCancelled(true);
    }
    //DEPRECATED FEATURE! WILL BE REMOVED IN THE FUTURE
    /*if($this->config->get("no-move") == "false") {
      if(isset($this->afk[strtolower($player->getName())])) {
        unset($this->afk[strtolower($player->getName())]);
        $player->setDisplayName($name);
        $player->sendMessage(TF::GREEN . "You are no longer AFK!");
      }
    }
    if($this->config->get("no-move") == "true") {
      if(isset($this->afk[strtolower($player->getName())])) {
        $player->sendMessage(TF::RED . "You can't move while AFK!");
        $player->sendMessage(TF::GREEN . "Type /afk to start moving!");
        $event->setCancelled(true);
      }
    } */
  }

  public function onChat(PlayerChatEvent $event) {
    $player = $event->getPlayer();
    $name = $player->getName();
    if($this->config->get("no-chat") == "false") {
      if(isset($this->afk[strtolower($player->getName())])) {
        unset($this->afk[strtolower($player->getName())]);
        $player->setDisplayName($name);
        $player->sendMessage(TF::GREEN . "You are no longer AFK!");
      }
    }
    if($this->config->get("no-chat") == "true") {
      if(isset($this->afk[strtolower($player->getName())])) {
        $player->sendMessage(TF::RED . "You can't chat while AFK!");
        $player->sendMessage(TF::GREEN . "Type /afk to start chatting!");
        $event->setCancelled(true);
      }
    }
  }

  public function onDamage(EntityDamageEvent $event) {
    if($event->getEntity() instanceof Player) {
      if(isset($this->afk[strtolower($event->getEntity()->getName())])){
        $event->setCancelled(true);
      }
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
        if(isset($this->afk[strtolower($sender->getName())])) {
          unset($this->afk[strtolower($sender->getName())]);
          $sender->setDisplayName($name);
          $sender->sendMessage(TF::GREEN . "You are no longer AFK!");
        } else {
          $this->afk[strtolower($sender->getName())] = strtolower($sender->getName());
          $sender->setDisplayName(TF::YELLOW . "[AFK] " . $name);
          $sender->sendMessage(TF::GREEN . "You are now AFK!");
        }
        break;
    }
    return true;
  }
}
