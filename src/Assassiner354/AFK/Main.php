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
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener {

  public $afk = [];
  
  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    @mkdir($this->getDataFolder());
  }
  
  public function onQuit(PlayerQuitEvent $event) {
    $player = $event->getPlayer();
    if(in_array($this->afk[strtolower($player->getName())])) {
      unset($this->afk[strtolower($player->getName())]);
    }
  }
  
  public function onMove(PlayerMoveEvent $event) {
    $player = $event->getPlayer();
    $name = $player->getName();
    if(in_array($this->afk[strtolower($player->getName())])) {
      unset($this->afk[strtolower($player->getName())]);
      $player->setDisplayName($name);
      $player->sendMessage(TF::GREEN . "You are no longer AFK!");
    }
  }
  
  public function onChat(PlayerChatEvent $event) {
    $player = $event->getPlayer();
    $name = $player->getName();
    if(in_array($this->afk[strtolower($player->getName())])) {
      unset($this->afk[strtolower($player->getName())]);
      $event->setCancelled();
      $player->sendMessage(TF::RED . "You can't chat while AFK!");
  }

  public function onDamage(EntityDamageEvent $event) {
    if($event->getEntity() instanceof Player && in_array($this->afk[strtolower($player->getName())])) {
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
        if(in_array($this->afk[strtolower($sender->getName())])) {
          unset($this->afk[strtolower($sender->getName())]);
          $player->setDisplayName($name);
          $player->sendMessage(TF::GREEN . "You are no longer AFK!");
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
