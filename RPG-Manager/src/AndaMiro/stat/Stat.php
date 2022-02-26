<?php

namespace AndaMiro\stat;

use pocketmine\player\Player;

abstract class Stat{

  protected const STAT_POINT = 3; //Points to level Up

  protected int $level = 0; //Stat's level

  public function __construct(int $level = 0){
    $this->level = $level;
  }

  abstract public function getName() : string;

  public static function getPointPerStat() : int{
    return self::STAT_POINT;
  }

/**
    gain power from the stat
**/

  public function getDamage() : float{
    return $this->getLevel() * $this::STAT_DAMAGE;
  }

  public function getDefense() : float{
    return $this->getLevel() * $this::STAT_DEFENSE;
  }

  public function getHealth() : float{
    return $this->getLevel() * $this::STAT_HEALTH;
  }

  public function getCriticalDamage() : float{
    return $this->getLevel() * $this::STAT_CRITICAL_DAMAGE;
  }

  public function getCriticalPercent() : float{
    return $this->getLevel() * $this::STAT_CRITICAL_PERCENT;
  }

  public function getAbilityDamage() : float{
    return $this->getLevel() * $this::STAT_ABILITY_DAMAGE;
  }

/**
    gain power from the stat (END)
**/

  public function getLevel() : int{
    return $this->level;
  }

  public function addLevel(Player $player, int $level) : void{
    $this->level += $level;
    $statManager = StatManager::getStatManager($player);
    $statManager->reducePoint($level * $this::STAT_POINT);
    for($i = 0; $i < $level; $i++) $statManager->OnStatLevelUp($this);
  }
}
