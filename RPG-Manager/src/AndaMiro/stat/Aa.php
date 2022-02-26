<?php

namespace AndaMiro\stat;

class Aa extends Stat{

  protected const STAT_DAMAGE = 0; //Damage per stat
  protected const STAT_DEFENSE = 0; //Defense per stat
  protected const STAT_HEALTH = 0; //Health per stat
  protected const STAT_CRITICAL_DAMAGE = 0.25; //Critical damage per stat
  protected const STAT_CRITICAL_PERCENT = 0.7; //Critical percent per stat
  protected const STAT_ABILITY_DAMAGE = 0; //Ability damage per stat

  public function getName() : string{
    return "Aa";
  }
}
