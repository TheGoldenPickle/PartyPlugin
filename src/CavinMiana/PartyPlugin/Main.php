<?php
/*
  ______             _     _____           _           _       
 |  ____|           | |   |  __ \         (_)         | |      
 | |__ _ __ ___  ___| |__ | |__) | __ ___  _  ___  ___| |_ ____
 |  __| '__/ _ \/ __| '_ \|  ___/ '__/ _ \| |/ _ \/ __| __|_  /
 | |  | | |  __/\__ \ | | | |   | | | (_) | |  __/ (__| |_ / / 
 |_|  |_|  \___||___/_| |_|_|   |_|  \___/| |\___|\___|\__/___|
                                         _/ |                  
                                        |__/ 
*/                  
namespace CavinMiana\PartyPlugin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;


class Main extends PluginBase implements Listener{
	public $request = array();
	public function onEnable(){
		$this->getLogger()->info("Plugin Loaded");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
	}
	// Events for PartyPlugin
	public function onDamageByPlayer(EntityDamageEvent $ev){
		$cause = $ev->getCause();
		switch($cause){
			case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
				$atkr = $ev->getDamager();
				$player = $ev->getEntity();
				$pl = $ev->getPlayer();
				if($atkr instanceof Player and $player instanceof Player){
					if($this->inParty($player, $atkr->getName())){
						$ev->setCancelled();
						$atkr->sendMessage(TextFormat::RED."$pl is in your party!");
					}
				}
		}
		break;
	}
	// Commands for PartyPlugin
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "party":
				if($sender instanceof Player){
					if(isset($args[0])){
						switch($args[0]){
							case "add":
								if($sender->hasPermission("party.add")){
									if(isset($args[1])){
										$player = $this->getServer()->getPlayer($args[1]);
										if(!$player == null){
											$this->sendRequest($player, $sender);
										}	else{
											$sender->sendMessage(TextFormat::RED."Player was not invited to party because player was not found");
										}
									}
									return;
								}{
									$sender->sendMessage(TextFormat::RED."You dont have permissiond to do this command!");
								}
								break;
								case "list":
								if($sender->hasPermission("party.list")){
									$config = new Config($this->getDataFolder()."players/". strtolower($sender->getName()).".yml", Config::YAML);
									$array = $config->get("party", []);
									$sender->sendMessage(TextFormat::GOLD.TextFormat::BOLD."Party:");
									foreach($array as $partyname){
										$sender->sendMessage(TextFormat::GREEN."* ".$friendname);
									}
									return;
								}else {
									$sender->sendMessage(TextFormat::RED."You do not have permission for that command");
								}
								break;
								// More Todo. Ill work on this tommorow
						}
					}}else{
						$sender->sendMessage("You command in-game");
				}
				break;
			case "accept":
				if($sender->hasPermission("party.accept")){
					if(in_array($sender->getName(), $this->request)){
						foreach($this->request as $target => $request){
							$target = $this->getServer()->getPlayer($target);
							$request = $this->getServer()->getPlayer($request);
							echo $target->getName().$request->getName();
							if($request->getName() === $sender->getName()){
								$this->addToParty($target, $request);
								$this->addToParty($request, $target);
							}
						}
						return;
					}else{
						$sender->sendMessage(TextFormat::GREEN."No pending party request yet ;)");
					}
					return;
				}else{
					$sender->sendMessage(TextFormat::RED."You dont have permission for that command");
				}
				break;
			case "sethome":
				if($sender->hasPermission("party.sethome")){
					// More todo. Just finished some commands. Going to start on API and finish off commands tommorow.
				}
		}
	}
}
