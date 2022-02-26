<?php

namespace AndaMiro\stat;

class Atk extends Stat{

  protected const STAT_DAMAGE = 5; //Damage per stat
  protected const STAT_DEFENSE = 0; //Defense per stat
  protected const STAT_HEALTH = 0; //Health per stat
  protected const STAT_CRITICAL_DAMAGE = 0.5; //Critical damage per stat
  protected const STAT_CRITICAL_PERCENT = 0; //Critical percent per stat
  protected const STAT_ABILITY_DAMAGE = 0; //Ability damage per stat

  public function getName() : string{
    return "Atk";
  }
}
