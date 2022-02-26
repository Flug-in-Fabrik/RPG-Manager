<?php

namespace AndaMiro\level;

use pocketmine\player\Player;

use pocketmine\entity\ExperienceManager;

use pocketmine\scheduler\ClosureTask;

use AndaMiro\stat\StatManager;

use AndaMiro\{Manager, RpgManager};

final class LevelManager{

  private static bool $IsOnlineManager = true;

  /** @var LevelManager[] **/
  private static $levelManagers = [];

  private ?Player $owner;

  private ExperienceManager $experienceManager;

  private int $level;
  private int $exp;

  public function __construct(Player $owner, int $level, int $exp){
    [$this->owner, $this->experienceManager, $this->level, $this->exp] = [$owner, new ExperienceManager($owner), $level, $exp];
    self::$levelManagers[strtolower($owner->getName())] = $this;
    $this->updateExperienceProgress();
  }

  public static function freeAllLevelManagers() : void{
    foreach(self::$levelManagers as $levelManager) $levelManager->freeLevelManager();
    self::$IsOnlineManager = false;
  }

  public static function getLevelManager(Player $player) : self{
    if(isset(self::$levelManagers[strtolower($player->getName())])) return self::$levelManagers[strtolower($player->getName())];
    if(file_exists($src = RpgManager::getInstance()->getDataFolder() . "/Level/PlayerDatas/" . strtolower($player->getName()) . ".yml")){
      $data = yaml_parse(file_get_contents($src));
      return new self($player, $data["Level"], $data["Exp"]);
    }
    return new self($player, 0, 0);
  }

  private function _updateExperienceProgress() : void{
    $this->experienceManager->setXpLevel($this->getLevel());
    if($this->getExp() == 0) $this->experienceManager->setXpProgress(0);
    else $this->experienceManager->setXpProgress(!isset(ExpList::LEVEL_TO_EXP[$this->getLevel() + 1]) ? 1.0 : (min(1.0, $this->getExp() / ExpList::LEVEL_TO_EXP[$this->getLevel() + 1])));
  }

  public function updateExperienceProgress() : void{
    RpgManager::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $unused = 0) : void{
      $this->_updateExperienceProgress();
    }), 10);
  }

  private function Serialize() : string{
    return yaml_emit(["Level" => $this->level, "Exp" => $this->exp], YAML_UTF8_ENCODING);
  }

  public function freeLevelManager() : void{
    if(!self::$IsOnlineManager) return;
    file_put_contents(RpgManager::getInstance()->getDataFolder() . "/Level/PlayerDatas/" . strtolower($this->owner->getName()) . ".yml", $this->Serialize());
    unset(self::$levelManagers[strtolower($this->owner->getName())]);
  }

  public function canLevelUp() : bool{
    return isset(ExpList::LEVEL_TO_EXP[$this->getLevel() + 1]) && ExpList::LEVEL_TO_EXP[$this->getLevel() + 1] <= $this->getExp();
  }

  public function getExp() : int{
    return $this->exp;
  }

  public function getLevel() : int{
    return $this->level;
  }

  public function setLevel(int $level) : void{
    $this->level = $level;
    $this->updateExperienceProgress();
  }

  public function setExp(int $exp) : void{
    $this->exp = $exp;
    $this->updateExperienceProgress();
  }

  public function addExp(int $exp) : void{
    $this->setExp($this->getExp() + $exp);
    if($this->canLevelUp()) $this->OnLevelUp();
  }

  public function reduceExp(int $exp) : void{
    $this->setExp(max(0, $this->getExp() - $exp));
  }

  public function addLevel(int $level) : void{
    for($i = 0; $i < $level; $i++) $this->OnLevelUp();
  }

  public function OnLevelUp() : void{
    StatManager::getStatManager($this->owner)->addPoint(3);
    $this->setLevel($this->getLevel() + 1);
    $this->reduceExp(ExpList::LEVEL_TO_EXP[$this->getLevel()]);
    if($this->canLevelUp()) $this->OnLevelUp();
  }
}
