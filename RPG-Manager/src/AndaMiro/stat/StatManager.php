<?php

namespace AndaMiro\stat;

use pocketmine\player\Player;

use AndaMiro\{Manager, RpgManager};

final class StatManager{

  private static bool $IsOnlineManager = true;

  /** @var StatManager[] **/
  private static $statManagers = [];

  private ?Player $owner;

  private int $point; //The point to level up the stats

  /** @var Stat **/
  private Atk $atk;
  private Ap $ap;
  private Def $def;
  private Aa $aa;

  public function __construct(Player $owner, int $point, Atk $atk, Ap $ap, Def $def, Aa $aa){
    [$this->owner, $this->point, $this->atk, $this->ap, $this->def, $this->aa] = [$owner, $point, $atk, $ap, $def, $aa];
    self::$statManagers[strtolower($owner->getName())] = $this;
  }

  public static function freeAllStatManagers() : void{
    foreach(self::$statManagers as $statManager) $statManager->freeStatManager();
    self::$IsOnlineManager = false;
  }

  public static function getStatManager(Player $player) : self{
    if(isset(self::$statManagers[strtolower($player->getName())])) return self::$statManagers[strtolower($player->getName())];
    if(file_exists($src = RpgManager::getInstance()->getDataFolder() . "/Stat/PlayerDatas/" . strtolower($player->getName()) . ".yml")){
      $data = yaml_parse(file_get_contents($src));
      return new self($player, $data["Point"], new Atk($data["Atk"]), new Ap($data["Ap"]), new Def($data["Def"]), new Aa($data["Aa"]));
    }
    return new self($player, 0, new Atk(), new Ap(), new Def(), new Aa());
  }

  private function Serialize() : string{
    return yaml_emit(["Point" => $this->getPoint(), "Atk" => $this->atk->getLevel(), "Ap" => $this->ap->getLevel(), "Def" => $this->def->getLevel(), "Aa" => $this->aa->getLevel()], YAML_UTF8_ENCODING);
  }

  public function freeStatManager() : void{
    if(!self::$IsOnlineManager) return;
    file_put_contents(RpgManager::getInstance()->getDataFolder() . "/Stat/PlayerDatas/" . strtolower($this->owner->getName()) . ".yml", $this->Serialize());
    unset(self::$statManagers[strtolower($this->owner->getName())]);
  }

  public function setPoint(int $point) : void{
    $this->point = $point;
  }

  public function getPoint() : int{
    return $this->point;
  }

  public function addPoint(int $point) : void{
    $this->setPoint($this->getPoint() + $point);
  }

  public function reducePoint(int $point) : void{
    $this->setPoint(max(0, $this->getPoint() - $point));
  }

  public function getAtk() : Atk{
    return $this->atk;
  }

  public function getAp() : Ap{
    return $this->ap;
  }

  public function getDef() : Def{
    return $this->def;
  }

  public function getAa() : Aa{
    return $this->aa;
  }

  public function OnStatLevelUp(Stat $stat) : void{

  }

/**
gain power from the all stats
**/

  public function getStatDamage() : float{
    return $this->atk->getDamage() + $this->ap->getDamage() + $this->def->getDamage() + $this->aa->getDamage();
  }

  public function getStatDefense() : float{
    return $this->atk->getDefense() + $this->ap->getDefense() + $this->def->getDefense() + $this->aa->getDefense();
  }

  public function getStatHealth() : float{
    return $this->atk->getHealth() + $this->ap->getHealth() + $this->def->getHealth() + $this->aa->getHealth();
  }

  public function getStatCriticalDamage() : float{
    return $this->atk->getCriticalDamage() + $this->ap->getCriticalDamage() + $this->def->getCriticalDamage() + $this->aa->getCriticalDamage();
  }

  public function getStatCriticalPercent() : float{
    return $this->atk->getCriticalPercent() + $this->ap->getCriticalPercent() + $this->def->getCriticalPercent() + $this->aa->getCriticalPercent();
  }

  public function getStatAbilityDamage() : float{
    return $this->atk->getAbilityDamage() + $this->ap->getAbilityDamage() + $this->def->getAbilityDamage() + $this->aa->getAbilityDamage();
  }

  public function getCriticalDamage() : float{
    if(mt_rand(1, 1000) / 10 <= $this->getStatCriticalPercent()) return $this->getStatCriticalDamage();
    else return 0;
  }

/**
gain power from the all stats (END)
**/
}
