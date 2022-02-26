<?php

namespace AndaMiro\equipment;

use pocketmine\player\Player;

use pocketmine\entity\Attribute;

use pocketmine\scheduler\ClosureTask;

use AndaMiro\level\LevelManager;

use AndaMiro\job\JobManager;

use AndaMiro\{Manager, RpgManager};

final class EquipmentManager{

  private static bool $IsOnlineManager = true;

  /** @var EquipmentManager[] **/
  private static $equipmentManagers = [];

  /** @var Equipment[] **/
  private static $equipments = [];

  private ?Player $owner;

  private Equipment $helmet;
  private Equipment $chestPlate;
  private Equipment $leggins;
  private Equipment $boots;
  private Equipment $weapon;
  private Equipment $assistantWeapon;

  public function __construct(Player $owner, Equipment $helmet, Equipment $chestPlate, Equipment $leggins, Equipment $boots, Equipment $weapon, Equipment $assistantWeapon){
    [$this->owner, $this->helmet, $this->chestPlate, $this->leggins, $this->boots, $this->weapon, $this->assistantWeapon] = [$owner, $helmet, $chestPlate, $leggins, $boots, $weapon, $assistantWeapon];
    self::$equipmentManagers[strtolower($owner->getName())] = $this;
  }

  public static function loadEquipemts() : void{
    foreach(Manager::getAllFiles(RpgManager::getInstance()->getDataFolder() . "/Equipment/Equipments/") as $filename) self::$equipments[basename($filename, ".yml")] = Equipment::deSerialize(file_get_contents(RpgManager::getInstance()->getDataFolder() . "/Equipment/Equipments/" . $filename));
  }

  public static function saveEquipments() : void{
    foreach(self::$equipments as $id => $equipment) file_put_contents(RpgManager::getInstance()->getDataFolder() . "/Equipment/Equipments/" . $id . ".yml", $equipment->Serialize());
  }

  public static function addEquipment(Equipment $equipment) : void{
    self::$equipments[$equipment->getId()] = $equipment;
  }

  public static function removeEquipment(Equipment $equipment) : void{
    unlink(RpgManager::getInstance()->getDataFolder() . "/Equipment/Equipments/" . $equipment->getId() . ".yml");
    unset(self::$equipments[$equipment->getId()]);
  }

  public static function getEquipment(string $id) : Equipment{
    return self::$equipments[$id] ?? new GhostEquipment();
  }

  public static function getEquipments() : array{
    return self::$equipments;
  }

  public static function freeAllEquipmentManagers() : void{
    foreach(self::$equipmentManagers as $equipmentManager) $equipmentManager->freeEquipmentManager();
    self::$IsOnlineManager = false;
  }

  public static function getEquipmentManager(Player $player) : self{
    if(isset(self::$equipmentManagers[strtolower($player->getName())])) return self::$equipmentManagers[strtolower($player->getName())];
    if(file_exists($src = RpgManager::getInstance()->getDataFolder() . "/Equipment/PlayerDatas/" . strtolower($player->getName()) . ".yml")){
      $data = yaml_parse(file_get_contents($src));
      return new self($player, self::getEquipment($data["Helmet"]), self::getEquipment($data["ChestPlate"]), self::getEquipment($data["Leggins"]), self::getEquipment($data["Boots"]), self::getEquipment($data["Weapon"]), self::getEquipment($data["AssistantWeapon"]));
    }
    return new self($player, new GhostEquipment(), new GhostEquipment(), new GhostEquipment(), new GhostEquipment(), new GhostEquipment(), new GhostEquipment());
  }

  private function Serialize() : string{
    return yaml_emit(["Helmet" => $this->helmet->getId(), "ChestPlate" => $this->chestPlate->getId(), "Leggins" => $this->leggins->getId(), "Boots" => $this->boots->getId(), "Weapon" => $this->weapon->getId(), "AssistantWeapon" => $this->assistantWeapon->getId()], YAML_UTF8_ENCODING);
  }

  public function freeEquipmentManager() : void{
    if(!self::$IsOnlineManager) return;
    file_put_contents(RpgManager::getInstance()->getDataFolder() . "/Equipment/PlayerDatas/" . strtolower($this->owner->getName()) . ".yml", $this->Serialize());
    unset(self::$equipmentManagers[strtolower($this->owner->getName())]);
  }

  public function setHelmet(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->helmet = $equipment;
      return;
    }
    if(!$equipment instanceof Helmet){
      RpgManager::sendMessage($this->owner, "not-helmet-equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-helmet");
      return;
    }
    $this->helmet = $equipment;
  }

  public function setChestPlate(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->chestPlate = $equipment;
      return;
    }
    if(!$equipment instanceof ChestPlate){
      RpgManager::sendMessage($this->owner, "not-chestplate-equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-chestplate");
      return;
    }
    $this->chestPlate = $equipment;
  }

  public function setLeggins(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->leggins = $equipment;
      return;
    }
    if(!$equipment instanceof Leggins){
      RpgManager::sendMessage($this->owner, "not_leggins_equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-leggins");
      return;
    }
    $this->leggins = $equipment;
  }

  public function setBoots(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->boots = $equipment;
      return;
    }
    if(!$equipment instanceof Boots){
      RpgManager::sendMessage($this->owner, "not-boots-equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-boots");
      return;
    }
    $this->boots = $equipment;
  }

  public function setWeapon(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->weapon = $equipment;
      return;
    }
    if(!$equipment instanceof Weapon){
      RpgManager::sendMessage($this->owner, "not-weapon-equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-weapon");
      return;
    }
    $this->weapon = $equipment;
  }

  public function setAssistantWeapon(Equipment $equipment) : void{
    if($equipment instanceof GhostEquipment){
      $this->assistantWeapon = $equipment;
      return;
    }
    if(!$equipment instanceof AssistantWeapon){
      RpgManager::sendMessage($this->owner, "not-assistantweapon-equipment");
      return;
    }
    $jobManager = JobManager::getJobManager($this->owner);
    if($jobManager->getJob()->getName() !== $equipment->getJob()){
      RpgManager::sendMessage($this->owner, "not-equals-job");
      return;
    }
    $levelManager = LevelManager::getLevelManager($this->owner);
    if(!$equipment instanceof GhostEquipment && $equipment->getMinLevel() > $levelManager->getLevel() or $equipment->getMaxLevel() < $levelManager->getLevel()){
      RpgManager::sendMessage($this->owner, "not-enough-level-assistantweapon");
      return;
    }
    $this->assistantWeapon = $equipment;
  }

  private function _updateEquipment() : void{
    $armorInv = $this->owner->getArmorInventory();
    if($armorInv->getHelmet()->getId() == 0) $this->setHelmet(new GhostEquipment());
    else $this->setHelmet(self::getEquipment($armorInv->getHelmet()->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $armorInv->getHelmet()->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));

    if($armorInv->getChestplate()->getId() == 0) $this->setChestPlate(new GhostEquipment());
    else $this->setChestPlate(self::getEquipment($armorInv->getChestplate()->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $armorInv->getChestplate()->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));

    if($armorInv->getLeggings()->getId() == 0) $this->setLeggins(new GhostEquipment());
    else $this->setLeggins(self::getEquipment($armorInv->getLeggings()->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $armorInv->getLeggings()->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));

    if($armorInv->getBoots()->getId() == 0) $this->setBoots(new GhostEquipment());
    else $this->setBoots(self::getEquipment($armorInv->getBoots()->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $armorInv->getBoots()->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));
    $inv = $this->owner->getInventory();

    if($inv->getItem(4)->getId() == 0) $this->setWeapon(new GhostEquipment());
    else $this->setWeapon(self::getEquipment($inv->getItem(4)->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $inv->getItem(4)->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));

    if($inv->getItem(5)->getId() == 0) $this->setAssistantWeapon(new GhostEquipment());
    else $this->setAssistantWeapon(self::getEquipment($inv->getItem(5)->getNamedTag()->getTag("AD_Equipment_Id") == null ? -1 : $inv->getItem(5)->getNamedTag()->getTag("AD_Equipment_Id")->getValue()));

    $this->owner->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->setValue(min($this->owner->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getMaxValue(), $this->owner->getAttributeMap()->get(Attribute::MOVEMENT_SPEED)->getDefaultValue() + $this->getEquipmentSpeed() * 0.03));
  }

  public function updateEquipment() : void{ //Called when equipment data is updated
    RpgManager::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $unused = 0) : void{
      $this->_updateEquipment();
    }), 10);
  }

/**
gain power from the all equipments
**/

  public function getEquipmentDamage() : float{
    return $this->helmet->getDamage() + $this->chestPlate->getDamage() + $this->leggins->getDamage() + $this->boots->getDamage() + $this->weapon->getDamage() + $this->assistantWeapon->getDamage();
  }

  public function getEquipmentDefense() : float{
    return $this->helmet->getDefense() + $this->chestPlate->getDefense() + $this->leggins->getDefense() + $this->boots->getDefense() + $this->weapon->getDefense() + $this->assistantWeapon->getDefense();
  }

  public function getEquipmentAbilityDamage() : float{
    return $this->helmet->getAbilityDamage() + $this->chestPlate->getAbilityDamage() + $this->leggins->getAbilityDamage() + $this->boots->getAbilityDamage() + $this->weapon->getAbilityDamage() + $this->assistantWeapon->getAbilityDamage();
  }

  public function getEquipmentSpeed() : float{
    return $this->helmet->getSpeed() + $this->chestPlate->getSpeed() + $this->leggins->getSpeed() + $this->boots->getSpeed() + $this->weapon->getSpeed() + $this->assistantWeapon->getSpeed();
  }

/**
gain power from the all equipments (END)
**/
}
