<?php

namespace AndaMiro;

use pocketmine\plugin\PluginBase;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use pocketmine\item\{Item, ItemFactory};

use pocketmine\entity\Attribute;

use pocketmine\scheduler\ClosureTask;

use AndaMiro\Command\{EquipmentCommand, StatCommand, JobCommand};

use AndaMiro\equipment\EquipmentManager;
use AndaMiro\stat\StatManager;
use AndaMiro\level\LevelManager;
use AndaMiro\job\JobManager;

class RpgManager extends PluginBase{

  private static $instance = null;

  public const PREFIX = "[알림] ";

	public function onLoad() : void{
		self::$instance = $this;
	}

	public static function getInstance() : RpgManager{
		return self::$instance;
	}

  public static function sendMessage(Player $player, string $message) : void{
    $messages = yaml_parse(file_get_contents(RpgManager::getInstance()->getDataFolder() . "Messages.yml"));
    $player->sendMessage(self::PREFIX . $messages[$message]);
  }

  protected function onEnable() : void{
    @mkdir($this->getDataFolder() . "/Equipment/");
    @mkdir($this->getDataFolder() . "/Equipment/Equipments");
    @mkdir($this->getDataFolder() . "/Equipment/PlayerDatas");
    @mkdir($this->getDataFolder() . "/Stat/");
    @mkdir($this->getDataFolder() . "/Stat/PlayerDatas");
    @mkdir($this->getDataFolder() . "/Level/");
    @mkdir($this->getDataFolder() . "/Level/PlayerDatas");
    @mkdir($this->getDataFolder() . "/Job/");
    @mkdir($this->getDataFolder() . "/Job/PlayerDatas");

    foreach(["Messages.yml"] as $file) {
			$this->saveResource($file);
		}

    $this->getServer()->getCommandMap()->register("AndaMiroEquipmentCommand", new EquipmentCommand());
    $this->getServer()->getCommandMap()->register("AndaMiroStatCommand", new StatCommand());
    $this->getServer()->getCommandMap()->register("AndaMiroJobCommand", new JobCommand());
    $this->getServer()->getPluginManager()->registerEvents(new RpgEvent(), $this);

    EquipmentManager::loadEquipemts();
  }

  protected function onDisable() : void{
    EquipmentManager::saveEquipments();
    EquipmentManager::freeAllEquipmentManagers();
    StatManager::freeAllStatManagers();
    LevelManager::freeAllLevelManagers();
    JobManager::freeAllJobManagers();
  }
}
