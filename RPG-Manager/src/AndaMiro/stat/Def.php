<?php

namespace AndaMiro\stat;

class Def extends Stat{

  protected const STAT_DAMAGE = 0; //Damage per stat
  protected const STAT_DEFENSE = 0.5; //Defense per stat
  protected const STAT_HEALTH = 7; //Health per stat
  protected const STAT_CRITICAL_DAMAGE = 0; //Critical damage per stat
  protected const STAT_CRITICAL_PERCENT = 0; //Critical percent per stat
  protected const STAT_ABILITY_DAMAGE = 0; //Ability damage per stat

  public function getName() : string{
    return "Def";
  }
}
