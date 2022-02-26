<?php

namespace AndaMiro;

use pocketmine\player\Player;

use pocketmine\event\Listener;

use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent, PlayerMoveEvent, PlayerRespawnEvent, PlayerInteractEvent};

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\{InventoryTransactionPacket, ContainerClosePacket, MobEquipmentPacket};

use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

use AndaMiro\equipment\EquipmentManager;
use AndaMiro\stat\StatManager;
use AndaMiro\level\LevelManager;

class RpgEvent implements Listener{
	public function onQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		EquipmentManager::getEquipmentManager($player)->freeEquipmentManager();
		StatManager::getStatManager($player)->freeStatManager();
		LevelManager::getLevelManager($player)->freeLevelManager();
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		EquipmentManager::getEquipmentManager($event->getPlayer())->updateEquipment();
		LevelManager::getLevelManager($event->getPlayer())->updateExperienceProgress();
	}

	public function onRespawn(PlayerRespawnEvent $event) : void{
		EquipmentManager::getEquipmentManager($event->getPlayer())->updateEquipment();
		LevelManager::getLevelManager($event->getPlayer())->updateExperienceProgress();
	}

	public function onInteract(PlayerInteractEvent $event) : void{
		if($event->getItem()->getId() == 0){
			$event->getPlayer()->sendMessage("RESET");
			LevelManager::getLevelManager($event->getPlayer())->setLevel(0);
		}
		else{
			LevelManager::getLevelManager($event->getPlayer())->addExp(100);
			$event->getPlayer()->sendMessage("ADD");
		}
	}

	public function onFight(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		$damage = $event->getBaseDamage();
    if($event instanceof EntityDamageByEntityEvent){
			$damager = $event->getDamager();
	    if($damager instanceof Player){
				$statManager = StatManager::getStatManager($damager);
				$critical = $statManager->getCriticalDamage();
	      $damage += $statManager->getStatDamage() + EquipmentManager::getEquipmentManager($damager)->getEquipmentDamage() + $critical;
				if($critical > 0) $damager->sendPopup("크리티컬!");
	    }
		}
		if($entity instanceof Player){
			$statManager = StatManager::getStatManager($entity);
			$damage -= $statManager->getStatDefense() + EquipmentManager::getEquipmentManager($entity)->getEquipmentDefense();
		}
		$event->setBaseDamage(max(0, $damage));
  }
/*
	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$statManager = StatManager::getStatManager($player);
		$equipmentManager = EquipmentManager::getEquipmentManager($player);
		$text = "데미지 : " . ($statManager->getStatDamage() + $equipmentManager->getEquipmentDamage()) . "\n";
		$text .= "마법 : " . ($statManager->getStatAbilityDamage() + $equipmentManager->getEquipmentAbilityDamage()) . "\n";
		$text .= "방어력 : " . ($statManager->getStatDefense() + $equipmentManager->getEquipmentDefense()) . "\n";
		$text .= "이동속도 : " . ($equipmentManager->getEquipmentSpeed()) . "\n";
		$text .= "체력 : " . ($statManager->getStatHealth()) . "\n";
		$player->sendTip($text);
	}
*/
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof InventoryTransactionPacket or $packet instanceof ContainerClosePacket or $packet instanceof MobEquipmentPacket) EquipmentManager::getEquipmentManager($event->getOrigin()->getPlayer())->updateEquipment();
	}
}
