<?php

namespace AndaMiro\equipment;

use pocketmine\item\Item;

abstract class Equipment{

  protected Item $item;

  protected int $id;
  protected string $name;
  protected string $rank;
  protected string $info;

  protected float $atk;
  protected float $ap;
  protected float $def;
  protected float $aa;

  protected string $job;
  protected int $minLevel;
  protected int $maxLevel;

  public function __construct(Item $item, int $id, string $name, string $rank, string $info, int $atk, int $ap, int $def, int $aa, string $job, int $minLevel, int $maxLevel){
    [$this->item, $this->id, $this->name, $this->rank, $this->info, $this->atk, $this->ap, $this->def, $this->aa, $this->job, $this->minLevel, $this->maxLevel] = [$item, $id, $name, $rank, $info, $atk, $ap, $def, $aa, $job, $minLevel, $maxLevel];
  }

  abstract public function getPartsName() : string;

  public static function deSerialize(string $serial) : Equipment{
    $data = yaml_parse($serial);
    switch($data["Parts"]){
      case "Helmet" :
      return new Helmet(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "ChestPlate" :
      return new ChestPlate(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "Leggins" :
      return new Leggins(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "Boots" :
      return new Boots(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "Weapon" :
      return new Weapon(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "AssistantWeapon" :
      return new AssistantWeapon(Item::jsonDeSerialize($data["Item"]), (int)$data["Id"], (string)$data["Name"], (string)$data["Rank"], (string)$data["Info"], (int)$data["Atk"], (int)$data["Ap"], (int)$data["Def"], (int)$data["Aa"], (string)$data["Job"], (int)$data["MinLevel"], (int)$data["MaxLevel"]);

      case "GhostEquipment" :
      return new GhostEquipment();
    }
    return new GhostEquipment();
  }

  public function Serialize() : string{
    return yaml_emit(["Parts" => $this->getPartsName(), "Id" => $this->id, "Item" => $this->item->jsonSerialize(), "Name" => $this->name, "Rank" => $this->rank, "Info" => $this->info, "Atk" => $this->atk, "Ap" => $this->ap, "Def" => $this->def, "Aa" => $this->aa, "Job" => $this->job, "MinLevel" => $this->minLevel, "MaxLevel" => $this->maxLevel], YAML_UTF8_ENCODING);
  }

  public function getId() : int{
    return $this->id;
  }

  public function getName() : string{
    return $this->name;
  }

  public function getJob() : string{
    return $this->job;
  }

  public function getRank() : string{
    return $this->rank;
  }

  public function getMinLevel() : int{
    return $this->minLevel;
  }

  public function getMaxLevel() : int{
    return $this->maxLevel;
  }

  public function getInfo() : string{
    return $this->info;
  }

  public function getDamage() : float{
    return $this->atk;
  }

  public function getAbilityDamage() : float{
    return $this->ap;
  }

  public function getDefense() : float{
    return $this->def;
  }

  public function getSpeed() : float{
    return $this->aa;
  }

  public function getEquipmentItem() : Item{
    $item = clone $this->item;
    $item->getNamedTag()->setInt("AD_Equipment_Id", $this->getId());
    $item->setCustomName($this->getName());
    $item->setLore([
      "등급 : {$this->getRank()}",
      "설명 : {$this->getInfo()}\n",
      "공격력 : {$this->getDamage()}",
      "마법 : {$this->getAbilityDamage()}",
      "방어력 : {$this->getDefense()}",
      "민첩 : {$this->getSpeed()}",
      "직업전용 : {$this->getJob()}",
      "레벨제한 : {$this->getMinLevel()} ~ {$this->getMaxLevel()}"
    ]);
    return $item;
  }
}
