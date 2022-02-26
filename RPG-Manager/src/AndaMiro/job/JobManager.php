<?php

namespace AndaMiro\job;

use pocketmine\player\Player;

use AndaMiro\RpgManager;

final class JobManager{

  private static bool $IsOnlineManager = true;

  /** @var JobManager[] **/
  private static $jobManagers = [];

  private ?Player $owner;

  private Job $job;

  public function __construct(Player $owner, Job $job){
    [$this->owner, $this->job] = [$owner, $job];
    self::$jobManagers[strtolower($owner->getName())] = $this;
  }

  public static function freeAllJobManagers() : void{
    foreach(self::$jobManagers as $jobManager) $jobManager->freeJobManager();
    self::$IsOnlineManager = false;
  }

  public static function getJobManager(Player $player) : self{
    if(isset(self::$jobManagers[strtolower($player->getName())])) return self::$jobManagers[strtolower($player->getName())];
    if(file_exists($src = RpgManager::getInstance()->getDataFolder() . "/Job/PlayerDatas/" . strtolower($player->getName()) . ".yml")){
      $data = yaml_parse(file_get_contents($src));
      if($data["Job"] === "Warrior") return new self($player, new Warrior());
      if($data["Job"] === "Archer") return new self($player, new Archer());
      if($data["Job"] === "Wizard") return new self($player, new Wizard());
      if($data["Job"] === "Assassin") return new self($player, new Assassin());
      if($data["Job"] === "Citizen") return new self($player, new Citizen());
    }
    return new self($player, new Citizen());
  }

  public function setJob(Job $job) : void{
    $this->job = $job;
  }
  
  public function getJob() : Job{
    return $this->job;
  }

  private function Serialize() : string{
    return yaml_emit(["Job" => $this->job->getName()], YAML_UTF8_ENCODING);
  }

  public function freeJobManager() : void{
    if(!self::$IsOnlineManager) return;
    file_put_contents(RpgManager::getInstance()->getDataFolder() . "/Job/PlayerDatas/" . strtolower($this->owner->getName()) . ".yml", $this->Serialize());
    unset(self::$jobManagers[strtolower($this->owner->getName())]);
  }
}
