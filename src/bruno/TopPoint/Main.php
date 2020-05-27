<?php

namespace bruno\TopPoint;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;

class Main extends PluginBase{

    private $particles = [];

    public function onEnable(){
     $this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
     if(empty($this->config["positions"])){
      $this->getServer()->getLogger()->Info("Please set the best position");
      return;
     }
     $pos = $this->config["positions"];
     $this->particles[] = new FloatingText($this, new Vector3($pos[0], $pos[1], $pos[2]));
     $this->getScheduler()->scheduleRepeatingTask(new UpdateTask($this), 100);
     $this->getServer()->getLogger()->Info("Main Point Position successfully loaded");
    }

    public function onCommand(CommandSender $p, Command $command, string $label, array $args):bool{
     if($command->getName() === "settoppoint"){
      if(!$p instanceof Player) return false;
      if(!$p->isOp()) return false;
      $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
      $config->set("positions", [round($p->getX()), round($p->getY()), round($p->getZ())]);
      $config->save();
      $p->sendMessage("§a* §oSuccessfully identified the list of the best point§r§f!");
     }
     return true;
    }

    public function getLeaderBoard():string{
     $data = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
     $point_top = $data->getAllPoint();
     $message = "";
     $toppoint = "§7[§gTop Point§7]\n";
     if(count($point_top) > 0){
      arsort($point_top);
      $i = 0;
      foreach($point_top as $name => $point){
       $message .= "§l".($i+1).". §b".$name." §f".$point." §b$"."\n";
       if($i >= 10){
        break;
       }
       ++$i;
      }
     }
     $return = (string) $toppoint.$message;
     return $return;
    }

    public function getParticles():array{
     return $this->particles;
    }

}