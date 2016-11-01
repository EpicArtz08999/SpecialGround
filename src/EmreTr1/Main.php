<?php
   // SpecialGround plugin by EmreTr1 
   // Copyright 2016
   
   namespace EmreTr1;
   
   use pocketmine\plugin\PluginBase;
   use pocketmine\Server;
   use pocketmine\event\Listener;
   use pocketmine\event\player\PlayerMoveEvent;
   use pocketmine\Player;
   use pocketmine\command\Command;
   use pocketmine\command\CommandSender;
   use pocketmine\utils\Config;
   use pocketmine\level\Position;
   use pocketmine\command\ConsoleCommandSender;
   
   class Main extends PluginBase implements Listener{
   	
   	 private $prefix = "§a[§bSpecial§dGround§a]§r ";
   	 private $selections = [];
   	 private $ongrounds = [];
   	 private $usages=[
   	 "edit.command"=>"§eUsage: /spg edit command <ground name> <command>",
   	 "edit.usage"=>"§eUsage: /spg edit <command|(more edits coming soon)>",
   	 "create.error"=>"§cYou must until select post!",
   	 "create.usage"=>"§eUsage: /spg create <ground name>",
   	 "remove.usage"=>"§eUsage: /spg remove <ground name>",
   	 "help"=>"§aSpecial§bGround §6Help Page\n§7- /spg pos1 :§eSelect the pos1\n§7- /spg pos2 :§eSelect the pos2\n§7- /spg create <groundname> :§eCreate a Ground\n§7- /spg edit <command|(more edits soon)> :§e Edit Ground settings\n§7- /spg remove <groundname> :§eRemove a Ground"];
   	 
   	 public function onEnable(){
   	 	$this->getServer()->getLogger($this->prefix."§ePLUGI ENABLED!");
   	 	$this->getServer()->getPluginManager()->registerEvents($this, $this);
   	 	@mkdir($this->getDataFolder());
   	 	$this->config=new Config($this->getDataFolder()."config.yml", Config::YAML);
   	 }
   	 
   	 public function onCommand(CommandSender $p, Command $cmd, $label, array $args){
   	 	if(!$p->hasPermission("scg.command.use")){
   	 		return false;
   	 	}
   	 	if(!empty($args[0])){
   	 		switch($args[0]){
   	 			case "pos1":
   	 			    $this->selections[$p->getName()]["pos1"]=new Position($p->x, $p->y - 1, $p->z, $p->getLevel());
   	 			    $p->sendMessage($this->prefix."§aPos1 selected!");
   	 			    break;
   	 			case "pos2":
   	 			    $this->selections[$p->getName()]["pos2"]=new Position($p->x, $p->y - 1, $p->z, $p->getLevel());
   	 			    $p->sendMessage($this->prefix."§aPos2 selected!");
   	 			    break;
   	 			case "create":
   	 			    if(!empty($args[1])){
   	 			    	if(isset($this->selections[$p->getName()]["pos1"]) and isset($this->selections[$p->getName()]["pos2"])){
   	 			    		$pos1=$this->selections[$p->getName()]["pos1"];
   	 			    		$pos2=$this->selections[$p->getName()]["pos2"];
   	 			    		$level=$pos1->getLevel()->getFolderName();
   	 			    		$opt=[
   	 			    		"command"=>"",
   	 			    		"commandtype"=>"Player",
   	 			    		/*"teleport"=>[
   	 			    		"x"=>0,
   	 			    		"y"=>0,
   	 			    		"z"=>0],*/
   	 			    		"pos1"=>[
   	 			    		"x"=>$pos1->x,
   	 			    		"y"=>$pos1->y,
   	 			    		"z"=>$pos1->z],
   	 			    		"pos2"=>[
   	 			    		"x"=>$pos2->x,
   	 			    		"y"=>$pos2->y,
   	 			    		"z"=>$pos2->z],
   	 			    		"level"=>$level];
   	 			    		$this->config->setNested("Grounds.$args[1]", $opt);
   	 			    		$this->config->save();
   	 			    		$p->sendMessage($this->prefix."§aSpecialGround created!");
   	 			    		unset($this->selections[$p->getName()]);
   	 			    	}else{
   	 			    		$p->sendMessage($this->prefix.$this->usages["create.error"]);
   	 			    	}
   	 			    }else{
   	 			    	$p->sendMessage($this->prefix.$this->usages["create.usage"]);
   	 			    }
   	 			    break;
   	 			case "edit":
   	 			    if(!empty($args[1])){
   	 			    	switch($args[1]){
   	 			    		case "command":
   	 			    		    if(!empty($args[2]) and !empty($args[3]) and $this->config->getNested("Grounds.$args[2]")){
   	 			    		    	$ground=$args[2];
   	 			    		    	array_shift($args);
   	 			    		    	array_shift($args);
   	 			    		    	array_shift($args);
   	 			    		    	$command=trim(implode(" ", $args));
   	 			    		    	$this->config->setNested("Grounds.$ground.command", $command);
   	 			    		    	$this->config->save();
   	 			    		    	$p->sendMessage($this->prefix."§aGround edited: command");
   	 			    		    }else{
   	 			    		    	$p->sendMessage($this->prefix.$this->usages["edit.command"]);
   	 			    		    }
   	 			    		    break;
   	 			    		/*case "teleport":
   	 			    		    if(!empty($args[2]) and !empty($args[3]) and $this->config->getNested("Grounds.$args[2]")){
   	 			    		    	$command=$args[3];
   	 			    		    	$ground=$args[2];
   	 			    		    	$this->config->setNested("Grounds.$ground.command", $command);
   	 			    		    	$this->config->save();
   	 			    		    	$p->sendMessage($this->prefix."§aGround edited: command");
   	 			    		    }else{
   	 			    		    	$p->sendMessage($this->prefix."§eUsage: /spg edit command <ground name> <command>");
   	 			    		    }
   	 			    		    break;*/
   	 			    	}
   	 			    }else{
   	 			    	$p->sendMessage($this->prefix.$this->usages["edit.usage"]);
   	 			    }
   	 			    break;
   	 			case "remove":
   	 			    if(!empty($args[1]) and $this->config->getNested("Grounds.$args[1]")){
   	 			    	$grounds=$this->config->getNested("Grounds");
   	 			    	unset($grounds[$args[1]]);
   	 			    	$this->config->set("Grounds", $grounds);
   	 			    	$this->config->save();
   	 			    	$p->sendMessage($this->prefix."§cGround Removed.");
   	 			    }else{
   	 			    	$p->sendMessage($this->prefix.$this->usages["remove.usage"]);
   	 			    }
   	 			    break;
   	 			default:
   	 			  $p->sendMessage($this->usages["help"]);
   	 			  break;
   	 		}
   	 	}else{
   	 		$p->sendMessage($this->usages["help"]);
   	 	}
   	 }
   	 
   	 public function onMove(PlayerMoveEvent $event){
   	 	$p=$event->getPlayer();
   	 	$grounds=$this->config->get("Grounds");
   	 	if($grounds){
   	 		foreach($grounds as $ground){
   	 			$pos1=$ground["pos1"];
   	 			$pos2=$ground["pos2"];
   	 			$command=$ground["command"];
   	 			$type=$ground["commandtype"];
   	 			$x1=$pos1["x"];
   	 			$x2=$pos2["x"];
   	 			$y1=$pos1["y"];
   	 			$y2=$pos2["y"];
   	 			$z1=$pos1["z"];
   	 			$z2=$pos2["z"];
   	 			if((min($x1,$x2) <= $p->x) && (max($x1,$x2) >= $p->x) && (min($y1,$y2) <= $p->y) && (max($y1,$y2) >= $p->y) && (min($z1,$z2) <= $p->z) && (max($z1,$z2) >= $p->z) and !isset($this->ongrounds[$p->getName()])){
   	 				 $this->ongrounds[$p->getName()]=true;
   					  if($command!="" and isset($this->ongrounds[$p->getName()])){
   					  	 $written=new ConsoleCommandSender();
   					  	 if($type=="Player" or $type=="player"){
   					  	 	 $written=$p;
   					  	 }
   					  	 $this->getServer()->dispatchCommand($written, str_ireplace("{player}", $p->getName(), $command));
   					  	 unset($this->ongrounds[$p->getName()]);
   					  }
   			  }
   	 		}
   	 	}
   	 }
   }
?>
